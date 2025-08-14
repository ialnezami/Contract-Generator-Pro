# Contract Generator Pro - Frontend

This is the Next.js frontend application for Contract Generator Pro, a powerful contract generation platform.

## 🚀 Quick Start

### Prerequisites
- Node.js 18+ 
- npm or yarn
- Git

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd contract-generator-pro
   ```

2. **Install dependencies**
   ```bash
   npm install
   # or
   yarn install
   ```

3. **Set up environment variables**
   ```bash
   cp env.example .env.local
   # Edit .env.local with your configuration
   ```

4. **Start the development server**
   ```bash
   npm run dev
   # or
   yarn dev
   ```

5. **Open your browser**
   Navigate to [http://localhost:3000](http://localhost:3000)

## 🏗️ Project Structure

```
src/
├── app/                    # Next.js 14 App Router
│   ├── auth/              # Authentication pages
│   │   ├── login/         # Login page
│   │   └── register/      # Registration page
│   ├── dashboard/         # Main dashboard
│   ├── contracts/         # Contract management
│   │   └── new/          # New contract creation
│   ├── layout.tsx         # Root layout
│   └── page.tsx           # Landing page
├── components/            # Reusable components
│   ├── ui/               # Base UI components
│   │   ├── button.tsx    # Button component
│   │   ├── card.tsx      # Card components
│   │   ├── input.tsx     # Input component
│   │   ├── label.tsx     # Label component
│   │   └── navigation.tsx # Navigation component
│   ├── auth/             # Authentication components
│   │   ├── LoginForm.tsx # Login form
│   │   └── RegisterForm.tsx # Registration form
│   └── contracts/        # Contract-related components
│       └── ContractForm.tsx # Contract creation form
├── lib/                  # Utility libraries
│   ├── api.ts            # API service
│   └── utils.ts          # Utility functions
├── store/                # State management
│   └── contract-store.ts # Zustand store
├── types/                # TypeScript type definitions
│   └── index.ts          # Main types
└── styles/               # Global styles
    └── globals.css       # Global CSS with Tailwind
```

## 🎨 UI Components

The application uses a custom design system built with:

- **Tailwind CSS** - Utility-first CSS framework
- **Radix UI** - Accessible component primitives
- **Lucide React** - Beautiful icons
- **Framer Motion** - Animation library

### Component Library

All UI components are located in `src/components/ui/` and follow a consistent design pattern:

- **Button** - Multiple variants (default, outline, secondary, etc.)
- **Card** - Content containers with header, content, and footer
- **Input** - Form input fields with validation states
- **Label** - Accessible form labels
- **Navigation** - Sidebar navigation with mobile support

## 🔐 Authentication

The application includes a complete authentication system:

- **Login Form** - Email/password authentication
- **Registration Form** - New user signup with validation
- **Protected Routes** - Dashboard and contract pages require authentication
- **Token Management** - JWT tokens stored in localStorage
- **Auto-logout** - Automatic logout on token expiration

### Authentication Flow

1. User visits `/auth/login` or `/auth/register`
2. Form submission calls the Laravel backend API
3. On success, user is redirected to `/dashboard`
4. JWT token is stored and used for subsequent API calls
5. Protected routes check authentication status

## 📄 Contract Management

### Contract Creation

The contract creation process includes:

1. **Template Selection** - Choose from available contract templates
2. **Variable Input** - Fill in template-specific fields
3. **Party Management** - Add/remove contract parties
4. **Preview Generation** - Generate contract preview
5. **Contract Creation** - Save contract to database

### Contract Dashboard

The dashboard provides:

- **Overview Statistics** - Total contracts, pending reviews, active contracts
- **Contract List** - Searchable and filterable contract list
- **Quick Actions** - View, edit, download, and delete contracts
- **Status Tracking** - Visual status indicators for each contract

## 🗃️ State Management

The application uses **Zustand** for state management with the following stores:

### Contract Store (`src/store/contract-store.ts`)

- User authentication state
- Contract data and filters
- Template data
- Loading and error states
- CRUD operations for contracts

### Store Features

- **Persistence** - User preferences saved to localStorage
- **DevTools** - Redux DevTools integration for debugging
- **Type Safety** - Full TypeScript support
- **Actions** - Centralized state mutations

## 🌐 API Integration

The frontend communicates with the Laravel backend through a centralized API service:

### API Service (`src/lib/api.ts`)

- **Authentication** - Login, register, logout, current user
- **Contracts** - CRUD operations, PDF generation
- **Templates** - Fetch available templates
- **Error Handling** - Centralized error handling and user feedback

### API Features

- **Interceptors** - Automatic token injection and error handling
- **Type Safety** - Full TypeScript support for API responses
- **Error Handling** - Consistent error messages and user feedback
- **Authentication** - Automatic token management

## 🎯 Key Features

### 1. Responsive Design
- Mobile-first approach
- Responsive navigation with collapsible sidebar
- Touch-friendly interface

### 2. Form Validation
- **React Hook Form** - Performant form handling
- **Zod** - Schema validation with TypeScript
- **Real-time Validation** - Immediate feedback on user input

### 3. Type Safety
- Full TypeScript implementation
- Strict type checking
- IntelliSense support in IDEs

### 4. Performance
- **Next.js 14** - Latest features and optimizations
- **Code Splitting** - Automatic route-based code splitting
- **Image Optimization** - Built-in image optimization
- **SEO** - Server-side rendering and meta tags

## 🧪 Testing

### Running Tests

```bash
# Unit tests
npm run test

# Watch mode
npm run test:watch

# Coverage report
npm run test:coverage

# E2E tests
npm run test:e2e
```

### Test Structure

- **Unit Tests** - Component and utility function testing
- **Integration Tests** - API integration testing
- **E2E Tests** - Full user workflow testing

## 🚀 Deployment

### Build for Production

```bash
npm run build
npm start
```

### Environment Variables

Ensure all required environment variables are set in production:

```bash
NEXT_PUBLIC_API_URL=https://api.contractgenerator.pro
NEXT_PUBLIC_APP_URL=https://contractgenerator.pro
NEXTAUTH_SECRET=your_production_secret
```

### Deployment Platforms

- **Vercel** - Recommended for Next.js applications
- **Netlify** - Alternative deployment option
- **AWS Amplify** - Enterprise deployment option

## 🔧 Development

### Code Style

- **ESLint** - Code linting and formatting
- **Prettier** - Code formatting
- **TypeScript** - Strict type checking

### Git Workflow

1. Create feature branch from `main`
2. Make changes with descriptive commits
3. Push branch and create pull request
4. Code review and approval
5. Merge to `main`

### Commit Convention

```
feat: add new contract template
fix: resolve authentication issue
docs: update README
style: format code with prettier
refactor: restructure API service
test: add unit tests for contract form
```

## 📚 Additional Resources

- [Next.js Documentation](https://nextjs.org/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Zustand Documentation](https://github.com/pmndrs/zustand)
- [React Hook Form Documentation](https://react-hook-form.com/)
- [Zod Documentation](https://zod.dev/)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Documentation** - Check this README and inline code comments
- **Issues** - Report bugs via GitHub Issues
- **Discussions** - Ask questions via GitHub Discussions
- **Email** - Contact the development team

---

**Happy coding! 🎉**
