# Custom Background Job Runner for Laravel

## About Background Job Runner

Background Job Runner is system that executes PHP classes as background jobs, independent of Laravel's built-in queue system.

## Features

1. Asynchronous Processing: Critical tasks like grading and notifications are handled independently, allowing students to get immediate feedback
   without burdening the System.
2. Prioritization and Delays: High-priority tasks like grading run first and if it fails, it is retried more than other jobs running as well, while
   lower-priority tasks like analytics run with delays and a deduction in retry value to reduce resource contention.
3. Detailed Monitoring: The dashboard provides a transparent view of all background jobs, allowing admins to monitor task completion, retry
   attempts.
    - performance analytics is an advanced feature that will add value to the system but wasn't implemented, it will require a specific use
      case whose performance need to be measured against the system and other information.
4. Enhanced Security: Only authorized job classes are executed in the background, ensuring the integrity and safety of the System.
5. Modularity: The application is designed in such a way the academic use case doesn't seep into the Background runner's domain, which makes it
   easier to make it a stand-alone system that other use case can make use of.

## Requirements

- PHP >= 8.2
- Composer
- Laravel 11.x
- MySQL

## Security Features

- Only pre-approved classes and methods can be executed as jobs.
- Input sanitization is performed on class and method names.

## Delays and Priority

- **Delay**: Set in seconds for job execution.
- **Priority**: Jobs can be assigned a priority (1 = High, 2 = Medium, 3 = Low).
    - A custom priority calculation is done by taking the absolute difference between the job's delay and retry value with the highest priority
      taking an additional retry and the lowest taking a deduction in retry in case of failure.

## Dashboard Features

- Jobs can be filtered for status and priority from the dashboard.
- Running Jobs can be seen from the dashboard.
- Running Jobs can be cancelled from the dashboard.
- Pending Jobs can be started from the dashboard.
- Failed and cancelled Jobs can be retried from the dashboard.
- Log of failed Jobs can be seen from the dashboard.

## Installation

1. **Clone the repository:**

   ```
   git clone git@github.com:okmarq/background-job-runner.git
   cd background-job-runner
   ```
2. **Install dependencies:**

    ```
    composer install
    ```
   ```
   npm install
   ```

3. **Set up environment:**

   Copy `.env.example` to `.env` and update database credentials adding the details below to your env file.

       ```
       For testing purposes only

       MAIL_MAILER=smtp
       MAIL_HOST=sandbox.smtp.mailtrap.io
       MAIL_PORT=2525
       MAIL_USERNAME=<your mailer.io username>
       MAIL_PASSWORD=<your mailer.io password>
       MAIL_ENCRYPTION=tls
       MAIL_FROM_ADDRESS=<your email address>
       MAIL_FROM_NAME="${APP_NAME}"
       TEST_PASSWORD=password
       ```

4. **Generate application key:**

    ```
    php artisan key:generate
    ```

5. **Run migrations and seed data:**

    ```
    php artisan migrate --seed
    ```

6. **Build the application front end:**

    ```
    npm run dev
    ```

7. **Serve the application:**

    ```
    php artisan serve
   ```

## Conclusion

- The Job runner is designed to be highly modular, scalable and simple to use in any given use case.
- The use case presented is academic where a student submits an assignment and the following is carried out by the job process:
    1. Grading,
    2. Student notification by email
    3. Course analytics
    4. Plagiarism check.
- For any given use case, a job can be
    1. Started
    2. Cancelled
    3. Retried
    4. Viewed
- The processing can only be accessed by a user with an Admin role

## License

This project is unlicensed.

## Contact

For questions or support, please contact [Joel Okoromi](mailto:okmarq@gmail.com)
