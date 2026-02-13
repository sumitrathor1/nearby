
<!-- Improved README.md: concise, actionable, and contributor-friendly -->

# 🏠 NearBy — Student Housing & Local Services

NearBy helps students discover safe, affordable accommodation and nearby services when they move to a new city. It's a lightweight PHP + MySQL app with role-based dashboards, listings, and a helpful chatbot.

---

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-brightgreen)](https://www.php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)](https://www.mysql.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

---

## ✨ What it does

- Provides verified accommodation listings with filters (location, rent, facilities)
- Lets property owners and service providers manage listings
- Offers a local services directory (tiffin, milk, groceries, vendors)
- Includes an integrated chatbot and map-based discovery
- Responsive UI suitable for mobile and desktop

---

## ⚡ Quick local setup

1. Clone:

```bash
git clone https://github.com/your-org/nearby.git
cd nearby
```

2. Create DB & import schema:

```bash
mysql -u root -p nearby_db < database/schema.sql
```

3. Configure DB credentials in `config/config.php` (or check `config/` files).

4. Run locally:

```bash
php -S localhost:8000
# open http://localhost:8000
```

---

## 🛠 Project structure

- `api/` — server endpoints and API helpers
- `controllers/` — server-side controllers
- `assets/` — CSS, JS, and images
- `database/` — SQL schema and sample data
- `config/` — configuration and DB connection

---

## 👩‍💻 Contributing

1. Create an issue to discuss the change.
2. Fork and create a branch: `git checkout -b feature/your-feature`.
3. Open a pull request and reference related issues.

See [CONTRIBUTING.md](CONTRIBUTING.md) for details.

---

## ✅ This change

- Rewrote README to be concise and onboarding-friendly (issue #62).

---

## 📬 Contact

Open an issue or reach out to repo maintainers for help.

---

Made with ❤️ for students and local communities.

