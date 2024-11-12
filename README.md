# Custom Background Job Runner for Laravel

## About Background Job Runner

Background Job Runner is system that executes PHP classes as background jobs, independent of Laravel's built-in queue system.

The background job runner works by using the helper function `runBackgroundJob` that accepts a background job whose fields contain the class, method,
parameters, output, retries, delay,
priority,
and status.

This job then has it's class name and method validated to ensure the class and method exists as well as checking if the names contain non-alphanumeric
characters.

To further strengthen the security of the application, only classes that are found in the `$allowedClasses` array of the `JobValidator` class are
processed.

Next the command is started according to the OS.
The command prioritizes the jobs it starts then runs the methods of the class it receives, logging relevant information about the process.

From the admin dashboard, a job can be started, cancelled, or retried depending on its status.

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
   - A middleware is used to separate the admin from the other users so only the admin can access the jobs and modify them according to what the 
     system determines.
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
8. **Set up logs**
    Create log files with the names specified below
   ```
    background_jobs.log
    background_jobs_errors.log
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

## Assumptions

- A dummy logic for prioritizing Jobs was used, in production a robust version will be implemented.
- A `cancelJob` function was implemented but is not functional due to the process ID not gotten from the running process.

## Testing and Logs

1. analytics.log
    - [2024-11-12 17:31:42] local.INFO: Generated analytics report {"course":"Data Structures","average_score":48,"assignment_count":1}
    - [2024-11-12 17:33:10] local.INFO: Generated analytics report {"course":"Software Engineering","average_score":21,"assignment_count":1}

2. assignment_processing.log
   - [2024-11-12 17:31:26] local.INFO: Assignment graded {"assignment":1,"course":"Data Structures","score":48,"user":"StudentF StudentL"}
   - [2024-11-12 17:33:03] local.INFO: Assignment graded {"assignment":2,"course":"Software Engineering","score":21,"user":"StudentF StudentL"}

3. background_jobs.log
   - [2024-11-12 17:31:26] local.INFO: Job 1 completed: App\Jobs\AutomatedGradingJob@handle {"parameters":"[1]","result":null,"status":"completed"}
   - [2024-11-12 17:31:41] local.INFO: Job 2 completed: App\Jobs\NotifyStudentOfSubmissionJob@handle {"parameters":"[1]","result":null,
     "status":"completed"}
   - [2024-11-12 17:31:42] local.INFO: Job 3 completed: App\Jobs\GenerateAnalyticsReportJob@handle {"parameters":"[\"Data Structures\"]",
     "result":null,"status":"completed"}
   - [2024-11-12 17:31:52] local.INFO: Job 4 completed: App\Jobs\CheckForPlagiarismJob@handle {"parameters":"[1]","result":null,"status":"completed"}
   - [2024-11-12 17:33:03] local.INFO: Job 5 completed: App\Jobs\AutomatedGradingJob@handle {"parameters":"[2]","result":null,"status":"completed"}
   - [2024-11-12 17:33:08] local.INFO: Job 6 completed: App\Jobs\NotifyStudentOfSubmissionJob@handle {"parameters":"[2]","result":null,
     "status":"completed"}
   - [2024-11-12 17:33:10] local.INFO: Job 7 completed: App\Jobs\GenerateAnalyticsReportJob@handle {"parameters":"[\"Software Engineering\"]",
     "result":null,"status":"completed"}
   - [2024-11-12 17:33:20] local.INFO: Job 8 completed: App\Jobs\CheckForPlagiarismJob@handle {"parameters":"[2]","result":null,"status":"completed"}

4. background_jobs_errors.log
   - [2024-11-12 17:34:26] local.ERROR: Job failed {"error":"No query results for model [App\\Models\\BackgroundJob] 9","status":"failed"}

5. plagiarism.log
   - [2024-11-12 17:31:52] local.ERROR: Checked for plagiarism {"assignment":1,"plagiarism_score":70,"status":"The Data Structures assignment was: 
   plagiarized with a score of: 70"}
   - [2024-11-12 17:33:20] local.CRITICAL: Checked for plagiarism {"assignment":2,"plagiarism_score":22,"status":"The Software Engineering 
     assignment was: not plagiarized with a score of: 22"}


## License

This project is unlicensed.

## Contact

For questions or support, please contact [Joel Okoromi](mailto:okmarq@gmail.com)
