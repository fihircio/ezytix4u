# Development Workflow Guide

## Development Environment Setup

### Required Tools
- PHP 8.1 or higher
- Composer
- Node.js & NPM
- MySQL 8.0 or higher
- Git
- VS Code (recommended) or your preferred IDE

### Local Development Setup
1. Clone the repository
```bash
git clone [repository-url]
cd [project-directory]
```

2. Install dependencies
```bash
composer install
npm install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure local database
```bash
# Update .env file with local database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_portal
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations and seeders
```bash
php artisan migrate --seed
```

6. Start development servers
```bash
# Terminal 1 - Laravel development server
php artisan serve

# Terminal 2 - Vite development server
npm run dev
```

## Git Workflow

### Branch Naming Convention
- Feature branches: `feature/description-of-feature`
- Bug fixes: `fix/description-of-bug`
- Hotfixes: `hotfix/description-of-fix`
- Releases: `release/version-number`

### Commit Message Convention
```
type(scope): description

[optional body]

[optional footer]
```

Types:
- feat: New feature
- fix: Bug fix
- docs: Documentation changes
- style: Code style changes
- refactor: Code refactoring
- test: Adding or modifying tests
- chore: Maintenance tasks

Example:
```
feat(auth): implement social login

- Add Google OAuth integration
- Add Facebook OAuth integration
- Update user model to support social login

Closes #123
```

### Pull Request Process
1. Create feature branch from develop
2. Make changes and commit following conventions
3. Push branch to remote
4. Create pull request to develop branch
5. Request code review
6. Address review comments
7. Merge after approval

## Coding Standards

### PHP
- Follow PSR-12 coding standards
- Use type hints and return types
- Write PHPDoc comments for classes and methods
- Keep methods small and focused
- Use dependency injection

Example:
```php
/**
 * Create a new event.
 *
 * @param array $data
 * @return Event
 * @throws ValidationException
 */
public function createEvent(array $data): Event
{
    $this->validate($data);
    return $this->eventRepository->create($data);
}
```

### JavaScript/Vue.js
- Follow Vue.js style guide
- Use ES6+ features
- Write component documentation
- Keep components small and focused
- Use Vuex for state management

Example:
```javascript
/**
 * Event card component
 * @component
 */
export default {
  name: 'EventCard',
  props: {
    event: {
      type: Object,
      required: true
    }
  },
  methods: {
    handleClick() {
      this.$emit('select', this.event)
    }
  }
}
```

## Testing

### PHP Tests
- Write unit tests for business logic
- Write feature tests for API endpoints
- Use factories for test data
- Follow AAA pattern (Arrange, Act, Assert)

Example:
```php
public function test_can_create_event()
{
    // Arrange
    $eventData = Event::factory()->make()->toArray();
    
    // Act
    $response = $this->postJson('/api/events', $eventData);
    
    // Assert
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description'
            ]
        ]);
}
```

### JavaScript Tests
- Write unit tests for Vue components
- Write integration tests for complex features
- Use Vue Test Utils
- Mock API calls

Example:
```javascript
import { mount } from '@vue/test-utils'
import EventCard from './EventCard.vue'

describe('EventCard', () => {
  it('emits select event when clicked', () => {
    const wrapper = mount(EventCard, {
      propsData: {
        event: {
          id: 1,
          title: 'Test Event'
        }
      }
    })
    
    wrapper.trigger('click')
    expect(wrapper.emitted().select).toBeTruthy()
  })
})
```

## Code Review Checklist

### General
- [ ] Code follows style guide
- [ ] Tests are written and passing
- [ ] Documentation is updated
- [ ] No security vulnerabilities
- [ ] Performance considerations addressed

### PHP
- [ ] PSR-12 compliance
- [ ] Type hints used
- [ ] PHPDoc comments present
- [ ] Error handling implemented
- [ ] Database queries optimized

### JavaScript
- [ ] Vue.js style guide compliance
- [ ] Component documentation
- [ ] State management appropriate
- [ ] Error handling implemented
- [ ] Performance optimized

## Deployment Process

### Staging
1. Create release branch
2. Run tests
3. Build assets
4. Deploy to staging
5. Run smoke tests
6. Get stakeholder approval

### Production
1. Merge to main branch
2. Create version tag
3. Run tests
4. Build assets
5. Deploy to production
6. Monitor for issues

## Troubleshooting

### Common Issues
1. Database connection issues
   - Check .env configuration
   - Verify database service is running
   - Check database credentials

2. Asset compilation issues
   - Clear node_modules and reinstall
   - Check Node.js version
   - Verify webpack configuration

3. API authentication issues
   - Check token generation
   - Verify token expiration
   - Check middleware configuration

### Debug Tools
- Laravel Debugbar
- Vue DevTools
- Browser Developer Tools
- Database Management Tool

## Performance Optimization

### Frontend
1. Lazy load components
2. Optimize images
3. Use code splitting
4. Implement caching
5. Minimize bundle size

### Backend
1. Optimize database queries
2. Implement caching
3. Use queue for heavy tasks
4. Optimize API responses
5. Monitor server resources 