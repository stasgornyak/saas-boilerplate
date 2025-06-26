# Demo project: Multi-tenant SaaS API Boilerplate

An application skeleton on top of which a multi-tenant SaaS API can be built.

## Main features
- Uses multi-database tenancy architecture.
- Implements JWT authentication.
- Has user/role/permission management.
- Includes license handling 
- Includes payment processing (Monobank or Fondy).

## API endpoints
### Users:
- register
- login/logout
- reset/change password
- get/update profile

### Tenants
- create
- get/list
- update
- delete
- sort

### Tenant users
- add user to tenant
- get/list
- update
- remove user from tenant

### Roles/permissions
- get permissions
- create
- get/list
- update
- delete
- sort

### Licenses
- get tariffs
- get/list
- purchase

### Payments
- create
- get/list
- get checkout url
- check payment status

## Other functionality
- Activate / Deactivate license jobs

## Testing
Prepare testing database:
```bash
php artisan test:create-tenant
```

Start cypress tests:
```bash
npx cypress run
```

Delete testing database:
```bash
php artisan test:create-tenant
```
