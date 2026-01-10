# pagespeed.world

A PageSpeed Insights monitoring tool built with Laravel 12 and Sail. Monitor your website performance with scheduled Lighthouse tests, view historical trends, and receive alerts for performance anomalies.

## Features

- **Multi-tenant Support**: Organizations can manage their own pages and users
- **Scheduled Crawling**: Automatically run PageSpeed Insights tests at configurable intervals
- **Mobile & Desktop Testing**: Separate results for mobile and desktop strategies
- **Real-time Charts**: Beautiful Apache ECharts graphs for performance trends
- **Admin Panel**: Full-featured Filament admin panel for platform management
- **Worker Monitoring**: Laravel Horizon for queue management and monitoring
- **Report Queue**: Weekly reports and anomaly detection with manual approval before sending
- **API Rate Limiting**: Respects Google PageSpeed Insights API quotas

## Requirements

- Docker Desktop
- PHP 8.2+ (for local development outside containers)
- Google PageSpeed Insights API Key

## Quick Start

1. **Clone the repository and navigate to it:**
   ```bash
   cd psi-monitoring
   ```

2. **Copy the environment file and add your PSI API key:**
   ```bash
   # Edit .env and set PSI_API_KEY=your_api_key_here
   ```

3. **Start the application:**
   ```bash
   ./vendor/bin/sail up -d
   ```

4. **Run migrations (if not already done):**
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

5. **Start the queue workers (Horizon):**
   ```bash
   ./vendor/bin/sail artisan horizon
   ```

6. **Access the application:**
   - User Dashboard: http://localhost/dashboard
   - Admin Panel: http://localhost/admin
   - Horizon Dashboard: http://localhost/horizon

## Default Credentials

After seeding:

**Admin User:**
- Email: admin@example.com
- Password: password

**Demo User:**
- Email: demo@example.com
- Password: password

## Architecture

### Models

- **Organization**: Tenant entity that owns pages and users
- **User**: Can be platform admin or organization admin
- **Page**: URL to monitor with configurable crawl intervals
- **CrawlResult**: PageSpeed Insights test results (mobile/desktop)
- **Report**: Weekly reports and anomaly alerts with approval workflow
- **ApiUsage**: Tracks daily API quota usage

### Jobs

- `AnalyzePageJob`: Runs a single PSI test for a page
- `ScheduledCrawlJob`: Dispatches crawl jobs for pages due for testing
- `GenerateWeeklyReportsJob`: Creates weekly performance reports
- `SendApprovedReportsJob`: Sends reports that have been approved

### Services

- `PageSpeedInsightsService`: Handles API calls with rate limiting

## Configuration

### Environment Variables

```env
# PageSpeed Insights API
PSI_API_KEY=your_api_key_here
PSI_DAILY_QUOTA=25000
PSI_REQUESTS_PER_MINUTE=400

# Horizon
HORIZON_PREFIX=pagespeed-world-horizon:
```

### Scheduling

The scheduler runs automatically and handles:
- Page crawling every 15 minutes (for pages due)
- Weekly report generation (Mondays at 8am)
- Sending approved reports hourly

To run the scheduler:
```bash
./vendor/bin/sail artisan schedule:work
```

### Queue Workers

The application uses two queue supervisors:
- `default`: General purpose jobs
- `psi`: PageSpeed Insights API calls (rate limited)

## Admin Panel Features

### Organizations
- Create/manage organizations
- Set page limits and minimum crawl intervals

### Users
- Manage platform and organization admins
- Assign users to organizations

### Pages
- Add URLs to monitor
- Configure crawl intervals
- Trigger manual crawls
- View latest scores

### Crawl Results
- View all test results
- Filter by page, strategy, status
- See detailed metrics

### Reports
- Review pending reports before sending
- Approve or reject with notes
- Bulk approval support

## Dashboard Features

### User Dashboard
- Overview of all monitored pages
- Performance score cards (mobile/desktop)
- Quick access to detailed page views

### Page Detail View
- Current scores (Performance, Accessibility, Best Practices, SEO)
- Historical performance trends with ECharts
- Core Web Vitals graphs (LCP, FCP, TBT, CLS)
- Mobile vs Desktop comparison

## API Quota Management

The application tracks daily API usage and:
- Prevents exceeding the daily quota
- Maintains a 1% buffer for safety
- Rate limits per-minute requests
- Displays quota status in the admin dashboard

## Development

### Running Tests
```bash
./vendor/bin/sail artisan test
```

### Clearing Caches
```bash
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan view:clear
```

### Rebuilding Assets
```bash
./vendor/bin/sail npm run build
```

## Ports

- **Application**: http://localhost (Port 80)
- **MySQL**: localhost:3306
- **Redis**: localhost:6379

## License

MIT
