<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Controller\HashingController;
use App\Entity\HashingBatch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HashingCommand extends Command
{
    protected const ARGUMENT_STRING_TO_HASH = 'string';
    protected const OPTION_CONSECUTIVE_REQUESTS = 'requests';

    protected static $defaultName = 'avato:test';

    protected static $defaultDescription = 'Generate md5 hashes from a string and number of requests';

    public function __construct(
        protected HttpClientInterface $client,
        protected RouterInterface $router,
        protected EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp(
                'This command allows you to provide a string and get an md5 hash as return.'
                . ' You must also pass the number of requests it should make in a row to generate the subsequent hashes.'
            )
            ->addArgument(self::ARGUMENT_STRING_TO_HASH, InputArgument::REQUIRED, 'A string you want to hash')
            ->addOption(
                name: self::OPTION_CONSECUTIVE_REQUESTS,
                mode: InputOption::VALUE_REQUIRED,
                description: 'How many times should the message be printed?',
                default: 1
            );
    }

    /**
     * @inheritDoc
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $consecutiveRequests = (int) $input->getOption(self::OPTION_CONSECUTIVE_REQUESTS);

        if ($consecutiveRequests < 1) {
            $output->writeln('Please provide a valid number of requests (>= 1)');
            return Command::FAILURE;
        }

        $hashingUrl = $this->router->generate(name: 'hash', referenceType: UrlGeneratorInterface::ABSOLUTE_URL);
        $stringToHash = $input->getArgument(self::ARGUMENT_STRING_TO_HASH);

        $startDate = new \DateTime();
        $iteration = 1;

        do {
            $response = $this->client->request(
                Request::METHOD_POST,
                $hashingUrl,
                ['body' => [HashingController::POST_PARAM_STRING_TO_HASH => $stringToHash]]
            );

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                continue;
            }

            $responseData = json_decode($response->getContent());

            $this->setUpOutput($output, $startDate, $iteration, $stringToHash, $responseData);
            $this->persistHashingBatch($startDate, $iteration, $stringToHash, $responseData);

            $stringToHash = $responseData->hash;
            $iteration++;
        } while ($iteration <= $consecutiveRequests);

        $this->entityManager->flush();

        return Command::SUCCESS;
    }

    protected function setUpOutput(
        OutputInterface $output,
        \DateTimeInterface $startDate,
        int $iteration,
        string $stringToHash,
        \stdClass $responseData
    ): void {
        $output->writeln("--- Iteration ${iteration} ({$startDate->format('Y-m-d H:i:s')}) ---");
        $output->writeln("String to hash: ${stringToHash}");
        $output->writeln("Hash: {$responseData->hash}");
        $output->writeln("Key: {$responseData->key}");
        $output->writeln("Attempts: {$responseData->attempts}");
        $output->writeln('------');
    }

    protected function persistHashingBatch(
        \DateTimeInterface $startDate,
        int $iteration,
        string $stringToHash,
        \stdClass $responseData
    ): void {
        $hashingBatch = (new HashingBatch())
            ->setStartDateTime($startDate)
            ->setIteration($iteration)
            ->setInputString($stringToHash)
            ->setGeneratedKey($responseData->key)
            ->setGeneratedHash($responseData->hash)
            ->setAttempts($responseData->attempts);

        $this->entityManager->persist($hashingBatch);
    }
}
