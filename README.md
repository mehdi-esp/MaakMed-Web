# MaakMed Web client
> MaakMed web client built with Symfony 5.4 and Tailwind CSS.

## Instructions

### Setup

1. Set the `DATABASE_URL` environment variable in `.env.local`, or use the default `DATABASE_URL` defined in `.env`.
2. Install dependencies
   ```bash
   npm ci
   composer i
   ```

### Running in development
1. Start the Symfony local dev server
   ```bash
   symfony serve
   ```
2. Start the Webpack Encore dev server
   ```bash
   npm run dev-server
   ```
