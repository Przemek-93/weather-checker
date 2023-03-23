<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Exception\CreateUserFailedException;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create new user.'
)]
class CreateUserCommand extends Command
{
    protected SymfonyStyle $style;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected UserPasswordHasherInterface $userPasswordHasher,
        protected UserRepository $userRepository,
        protected ValidatorInterface $validator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                name: 'password',
                mode: InputArgument::OPTIONAL,
                description: 'The plain password of the new user'
            )
            ->addOption(
                name: 'email',
                mode: InputArgument::OPTIONAL,
                description: 'The email of the new user'
            )
            ->addUsage('app:create-user --email test@test.pl --password secret');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->style = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if ($input->getOption('email') === null || $input->getOption('password') === null) {
            throw new CreateUserFailedException('Missed email or password!');
        }

        if ($this->userRepository->findOneBy(['email' => $input->getOption('email')])) {
            throw new CreateUserFailedException('User with this email address already exists!');
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->style->writeln('Creating user...');
            $user = (new User())
                ->setEmail($input->getOption('email'))
                ->setRoles(User::ROLE_USER);
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $input->getOption('password')
                )
            );
            $this->userValidation($user);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->style->success('User has been created!');

            return self::SUCCESS;
        } catch (Throwable $throwable) {
            $this->style->error($throwable->getMessage());

            return self::FAILURE;
        }
    }

    protected function userValidation(UserInterface $user): void
    {
        $validationErrors = $this->validator->validate($user);
        if (count($validationErrors)) {
            $errorsString = '';
            foreach ($validationErrors as $error) {
                $errorsString .= $error . ';';
            }

            throw new CreateUserFailedException(
                sprintf(
                    'User is not valid [%s]',
                    $errorsString
                )
            );
        }
    }
}
