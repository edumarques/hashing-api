<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\HashingBatch;
use App\Exception\RateLimiterException;
use App\Exception\RequestValidationException;
use App\Service\HashingService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;

final class HashingController
{
    public const POST_PARAM_STRING_TO_HASH = 'stringToHash';

    public function __construct(
        private HashingService $hashingService,
        private RateLimiterFactory $hashingApiLimiter,
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator
    ) {
    }

    /**
     * @throws \Exception
     */
    #[Route('/hash', name: 'hash', methods: ['POST'], format: 'json')]
    public function hash(Request $request): JsonResponse
    {
        $rateLimiter = $this->hashingApiLimiter->create($request->getClientIp());

        if (! $rateLimiter->consume()->isAccepted()) {
            throw new RateLimiterException(
                Response::HTTP_TOO_MANY_REQUESTS,
                'Too Many Attempts'
            );
        }

        $postParams = $request->request;

        $this->validateParams($postParams);

        $hashObject = $this->hashingService->generateCustomMd5($postParams->get(self::POST_PARAM_STRING_TO_HASH));

        return new JsonResponse([
            'hash' => $hashObject->getHash(),
            'key' => $hashObject->getKey(),
            'attempts' => $hashObject->getAttempts(),
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/hashing-batches/{page}', name: 'hashing-batches', requirements: ['page' => '\d+'], methods: ['GET'], format: 'json')]
    public function hashingBatches(Request $request, int $page = 1): JsonResponse
    {
        $hashingBatchRepo = $this->entityManager->getRepository(HashingBatch::class);

        $attemptsLessThan = ($attemptsLessThan = $request->get('attemptsLessThan')) ? (int) $attemptsLessThan : null;

        $pagination = $this->paginator->paginate(
            $attemptsLessThan
                ? $hashingBatchRepo->queryByAttemptsLessThan($attemptsLessThan)
                : $hashingBatchRepo->queryAll(),
            $page
        );

        $data = array_map(
            static function (HashingBatch $batch): array {
                return [
                    'startDateTime' => $batch->getStartDateTime()->format('Y-m-d H:i:s'),
                    'iteration' => $batch->getIteration(),
                    'inputString' => $batch->getInputString(),
                    'generatedKey' => $batch->getGeneratedKey(),
                ];
            },
            (array) $pagination->getItems()
        );

        return new JsonResponse([
            'pagination' => [
                'totalCount' => $pagination->getTotalItemCount(),
                'currentPage' => $pagination->getCurrentPageNumber(),
                'itemsPerPage' => $pagination->getItemNumberPerPage(),
            ],
            'data' => $data,
        ]);
    }

    /**
     * @throws \Exception
     */
    private function validateParams(InputBag $params): void
    {
        $validationConstraints = new Collection([
            self::POST_PARAM_STRING_TO_HASH => new NotBlank(message: 'Please provide a string to be hashed'),
        ]);

        $validator = Validation::createValidator();

        $violationList = $validator->validate($params->all(), $validationConstraints)->getIterator()->getArrayCopy();

        if (empty($violationList)) {
            return;
        }

        $exceptionMessage = array_reduce(
            $violationList,
            static fn (string $carry, ConstraintViolation $item): string => $carry . $item->getMessage() . '; ',
            ''
        );

        throw new RequestValidationException(Response::HTTP_BAD_REQUEST, $exceptionMessage);
    }
}
