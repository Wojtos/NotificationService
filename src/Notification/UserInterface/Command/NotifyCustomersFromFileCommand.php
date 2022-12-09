<?php

namespace App\Notification\UserInterface\Command;

use App\Notification\Domain\Entity\Customer;
use App\Notification\Domain\Entity\Customers;
use App\Notification\Domain\Entity\Notification;
use App\Notification\Domain\Entity\NotificationMessage;
use App\Notification\Domain\NotificationService;
use App\Notification\Domain\ValueObject\DeviceToken;
use App\Notification\Domain\ValueObject\Email;
use App\Notification\Domain\ValueObject\Language;
use App\Notification\Domain\ValueObject\PhoneNumber;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_map;
use function count;
use function fclose;
use function fgets;
use function fopen;
use function sprintf;
use function trim;

#[AsCommand(name: 'notification:from-file')]
final class NotifyCustomersFromFileCommand extends Command
{
    /** @var array<string> */
    private array $fileLines;

    public function __construct(
        private readonly NotificationService $notificationService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'fileNamePath',
            InputArgument::OPTIONAL,
            '',
            'notification_from_file_example.txt'
        );
    }

    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $fileNamePath = $input->getArgument('fileNamePath');
        $this->fileLines = $this->readFile($fileNamePath);
    }


    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $language = new Language('en');
        $notification = Notification::createNew(
            messagesByLanguage: [
                $language->toString() => new NotificationMessage(
                    language: $language,
                    title: 'example_title',
                    text: 'example_text'
                )
            ],
            fallBackLanguage: $language
        );
        try {
            $customers = $this->createCustomers();
            foreach ($customers as $customer) {
                $output->writeln(sprintf(
                    "Added customer(id=%s), email: %s, phoneNumber: %s, deviceToken: %s",
                    $customer->getId(),
                    $customer->getEmail()?->toString() ?? 'null',
                    $customer->getPhoneNumber()?->toString() ?? 'null',
                    $customer->getDeviceToken()?->toString() ?? 'null'
                ));
            }
            $this->notificationService->send(
                $customers,
                $notification
            );
        } catch (Exception $exception) {
            $output->write("ERROR: " . $exception->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }

    /** @return array<string> */
    private function readFile(string $fileNamePath): array
    {
        $lines = [];
        $handle = fopen($fileNamePath, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $lines[] = trim($line);
            }

            fclose($handle);
        }
        return $lines;
    }

    private function createCustomers(): Customers
    {
        return new Customers(
            array_map(
                fn(string $line): Customer => $this->createCustomer($line),
                $this->fileLines
            )
        );
    }

    private function createCustomer(string $line): Customer
    {
        $splitLines = explode(' ', $line);
        if (count($splitLines) !== 3) {
            throw new InvalidArgumentException('Every line has to have 3 words');
        }
        return Customer::createNew(
            language: new Language('pl'),
            email: $splitLines[0] != 'null' ? new Email($splitLines[0]) : null,
            phoneNumber: $splitLines[1] != 'null' ? new PhoneNumber($splitLines[1]) : null,
            deviceToken: $splitLines[2] != 'null' ? new DeviceToken($splitLines[2]) : null
        );
    }
}
