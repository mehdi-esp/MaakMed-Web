# MaakMed Web client
> MaakMed web client built with Symfony 5.4 and Tailwind CSS.

## Showcase

<details>
  <summary>Screenshots</summary>
  <table>
    <tr>
      <td>
        <img
          src="https://gist.githubusercontent.com/mehdi-esp/50c84e8bfbe73763f3d12c9050dc5293/raw/612906b1f0efb53ef0023048d6b819304d9e348d/home-visitor.png"
          alt="image"
        />
        <br />
        <sub><center>Home page</center></sub>
      </td>
    </tr>
  </table>
  <table>
    <tr>
      <td>
        <img
          src="https://gist.githubusercontent.com/mehdi-esp/50c84e8bfbe73763f3d12c9050dc5293/raw/612906b1f0efb53ef0023048d6b819304d9e348d/visits-doctor.png"
          alt="image"
        />
        <br />
        <sub><center>Doctor visit view</center></sub>
      </td>
    </tr>
  </table>
  <table>
    <tr>
      <td>
        <img
          src="https://gist.githubusercontent.com/mehdi-esp/50c84e8bfbe73763f3d12c9050dc5293/raw/612906b1f0efb53ef0023048d6b819304d9e348d/dashboard-visits.png"
          alt="image"
        />
        <br />
        <sub><center>Admin dashboard</center></sub>
      </td>
    </tr>
  </table>
</details>

## Instructions

### Setup

1. Set the `DATABASE_URL` environment variable in `.env.local`, or use the default `DATABASE_URL` defined in `.env`.
2. Install dependencies
    ```bash
    npm ci
    composer i
    ```
3. Define corresponding keys for each API function to ensure its activation.

    - Mailing & SMS:
        + `MAILER_DSN`
        + `TWILIO_DSN`
    - **Turnstile**:
        + `TURNSTILE_KEY`
        + `TURNSTILE_SECRET`
    - **Stripe**:
        + `STRIPE_KEY`
        + `ENDPOINT_SECRET`
    - **HuggingFace**:
        + `HF_API_TOKEN`
    - **File.io**:
        + `FILE_IO_API_KEY`
    - **DeepGram**
        + `DEEPGRAM_TOKEN`

### Running in development
1. Start the Symfony local dev server
    ```bash
    symfony serve
    ```
2. Start the Webpack Encore dev server
    ```bash
    npm run dev-server
    ```
3. Run the messenger consumer to enable email and SMS sending
    ```bash
    php bin/console messenger:consume async failed
    ```
