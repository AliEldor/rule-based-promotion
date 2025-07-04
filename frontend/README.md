# Rule-Based Promotion Engine Frontend

A modern React frontend for the rule-based promotion engine, built with Vite and Tailwind CSS.

## Features

### 🛒 Checkout Demo Page

- Real-time discount calculation as you select products and customers
- Dynamic rule evaluation using the backend API
- Clean, intuitive interface showing applied promotions and savings
- Sample products and customers for testing

### ⚙️ Rules Management

- View existing promotion rules in a clean table format
- Create new rules with JSON-based conditions and actions
- Rule status and priority management
- Simple form interface for rule creation

## Tech Stack

- **Frontend**: React 19 + Vite
- **Styling**: Tailwind CSS
- **HTTP Client**: Axios
- **Routing**: React Router DOM
- **Backend API**: Laravel (http://localhost:8000/api)

## Getting Started

1. **Install dependencies**:

   ```bash
   npm install
   ```

2. **Start the development server**:

   ```bash
   npm run dev
   ```

3. **Make sure the backend is running**:
   - Laravel API should be running on `http://localhost:8000`
   - The Node.js rule engine should be accessible via the Laravel API

## Sample Data

### Products

- Widget A ($100) - Electronics
- Gadget B ($80) - Electronics
- Flash Deal C ($120) - Clearance
- Home Essential D ($60) - Home

### Customers

- Alice Apple (restaurants, silver tier)
- Bob TechCorp (retail, gold tier)
- Charlie Smith (individual, bronze tier)

## API Integration

The frontend communicates with the Laravel backend using these endpoints:

- `GET /api/rules` - Fetch all promotion rules
- `POST /api/rules` - Create new promotion rule
- `POST /api/evaluate` - Evaluate cart against rules

## Key Components

- **CheckoutPage**: Main feature demonstrating real-time rule evaluation
- **RulesManagement**: View and create promotion rules
- **Navigation**: Clean routing between pages
- **API Service**: Centralized API communication

## Project Structure

```
src/
├── components/
│   ├── CheckoutPage.jsx      # Main checkout demo
│   └── RulesManagement.jsx   # Rules administration
├── services/
│   └── api.js                # API service layer
├── data/
│   └── sampleData.js         # Sample products and customers
├── App.jsx                   # Main app component with routing
├── main.jsx                  # React entry point
└── index.css                 # Tailwind CSS imports
```

## Development Notes

- Built for a 72-hour project timeline
- Focus on functionality over complex animations
- Professional appearance suitable for technical demos
- Responsive design with mobile support
- Error handling for API failures

## Backend Requirements

Make sure your Laravel backend is running with the required endpoints and CORS configured to allow requests from `http://localhost:5173` (Vite's default dev server).+ Vite

This template provides a minimal setup to get React working in Vite with HMR and some ESLint rules.

Currently, two official plugins are available:

- [@vitejs/plugin-react](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react) uses [Babel](https://babeljs.io/) for Fast Refresh
- [@vitejs/plugin-react-swc](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react-swc) uses [SWC](https://swc.rs/) for Fast Refresh

## Expanding the ESLint configuration

If you are developing a production application, we recommend using TypeScript with type-aware lint rules enabled. Check out the [TS template](https://github.com/vitejs/vite/tree/main/packages/create-vite/template-react-ts) for information on how to integrate TypeScript and [`typescript-eslint`](https://typescript-eslint.io) in your project.
