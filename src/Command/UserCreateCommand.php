<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Create user',
)]
class UserCreateCommand extends Command
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $manager,
        private readonly ValidatorInterface $validator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::OPTIONAL, 'Username')
            ->addArgument('lastname', InputArgument::OPTIONAL, 'Last Name')
            ->addArgument('firstname', InputArgument::OPTIONAL, 'First Name')
            ->addArgument('phone', InputArgument::OPTIONAL, 'phone')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password')
            ->addArgument('role', InputArgument::OPTIONAL, 'Role')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        /** @var string|null $lastName */
        $username = $input->getArgument('username');
        $usernameConstraint = [new Type('alnum'), new Length(max: 36)];
        if (null === $username) {
            $question = new Question($this->getDefinition()->getArgument('username')->getDescription());
            $question->setValidator(Validation::createCallable(...$usernameConstraint));
            $username = $io->askQuestion($question);
        }
        if (!$this->inputValidator($io, $username, $usernameConstraint)) {
            return Command::INVALID;
        }

        /** @var string|null $lastName */
        $lastName = $input->getArgument('lastname');
        $lastNameConstraint = [new Type('alnum'), new Length(max: 36)];
        if (null === $lastName) {
            $question = new Question($this->getDefinition()->getArgument('lastname')->getDescription());
            $question->setValidator(Validation::createCallable(...$lastNameConstraint));
            $lastName = $io->askQuestion($question);
        }
        if (!$this->inputValidator($io, $lastName, $lastNameConstraint)) {
            return Command::INVALID;
        }

        /** @var string|null $firstName */
        $firstName = $input->getArgument('firstname');
        $firstNameConstraint = [new Type('alnum'), new Length(max: 36)];
        if (null === $firstName) {
            $question = new Question($this->getDefinition()->getArgument('firstname')->getDescription());
            $question->setValidator(Validation::createCallable(...$firstNameConstraint));
            $firstName = $io->askQuestion($question);
        }
        if (!$this->inputValidator($io, $firstName, $firstNameConstraint)) {
            return Command::INVALID;
        }

        /** @var string|null $firstName */
        $phone = $input->getArgument('phone');
        $phoneConstraint = [new Type('alnum'), new Length(max: 10)];
        if (null === $phone) {
            $question = new Question($this->getDefinition()->getArgument('phone')->getDescription());
            $question->setValidator(Validation::createCallable(...$phoneConstraint));
            $phone = $io->askQuestion($question);
        }
        if (!$this->inputValidator($io, $phone, $phoneConstraint)) {
            return Command::INVALID;
        }

        /** @var string|null $email */
        $email = $input->getArgument('email');
        $emailConstraint = new Email();
        if (null === $email) {
            $question = new Question($this->getDefinition()->getArgument('email')->getDescription());
            $question->setValidator(Validation::createCallable($emailConstraint));
            $email = $io->askQuestion($question);
        }
        if (!$this->inputValidator($io, $email, $emailConstraint)) {
            return Command::INVALID;
        }

        $password = $input->getArgument('password');
        $passwordConstraint = new NotBlank();

        if (null === $password) {
            $question = new Question($this->getDefinition()->getArgument('password')->getDescription());
            $question->setValidator(Validation::createCallable($passwordConstraint))->setHidden(true)->setHiddenFallback(false);
            $password = $io->askQuestion($question);
        }
        if (!$this->inputValidator($io, $password, $passwordConstraint)) {
            return Command::INVALID;
        }

        $role = $input->getArgument('role');
        $roleConstraint = new NotBlank();
        if (null === $role) {
            $question = new Question($this->getDefinition()->getArgument('role')->getDescription());
            $question->setValidator(Validation::createCallable($roleConstraint));
            $role = $io->askQuestion($question);
        }
        if (!$this->inputValidator($io, $role, $roleConstraint)) {
            return Command::INVALID;
        }

        $user = new User();
        $user->setUsername($username);
        $user->setLastName($lastName);
        $user->setFirstName($firstName);
        $user->setPhone($phone);
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->addRole($role);

        $this->manager->persist($user);
        $this->manager->flush();

        $io->success('User created !');

        return Command::SUCCESS;
    }

    /**
     * @param Constraint|Constraint[] $constraints
     */
    private function inputValidator(SymfonyStyle $io, mixed $value, Constraint|array|null $constraints = null): bool
    {
        $violations = $this->validator->validate($value, $constraints);

        if (0 !== \count($violations)) {
            foreach ($violations as $violation) {
                $io->error($violation->getMessage());
            }

            return false;
        }

        return true;
    }
}
