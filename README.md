# Custom Background Job Runner for Laravel

## About Background Job Runner

Background Job Runner is system that executes PHP classes as background jobs, independent of Laravel's built-in queue system.

- Scalable
- Error handling capability
- Ease of use

## Security Features

- Only pre-approved classes and methods can be executed as jobs.
- Input sanitization is performed on class and method names.

## Delays and Priority

- **Delay**: Set a delay in seconds for job execution.
- **Priority**: Jobs can be assigned a priority (1 = High, 2 = Medium, 3 = Low).

## Dashboard Features

- Filter jobs by status and priority.
- Cancel running jobs from the dashboard.

## Example Usage with Priority and Delay

```php
runBackgroundJob(App\Jobs\ExampleJob::class, 'process', ['param1'], retries: 3, delay: 10, priority: 1);
```

### Summary

This architecture now offers a robust custom background job runner in Laravel that’s tailored for scalability and security.

- Security is increased by validating classes and methods.
- Job execution can be delayed or prioritized.
- The dashboard allows monitoring and managing jobs more effectively.

## Workflow

1. Asynchronous Processing: Critical tasks like grading and notifications are handled independently, allowing students to get immediate feedback
   without burdening the LMS.
2. Prioritization and Delays: High-priority tasks like grading run first, while lower-priority tasks (e.g., analytics) run with delays to reduce
   resource contention.
3. Detailed Monitoring: The dashboard provides a transparent view of all background jobs, allowing admins to monitor task completion, retry attempts,
   and performance analytics.
4. Enhanced Security: Only authorized job classes are executed in the background, ensuring the integrity and safety of the LMS.

## License

No license.
