# VitalBalance Design System Documentation
## Theme: Green + Beige (Nature Health)

---

## 📐 Design Philosophy

VitalBalance employs a **nature-inspired, medical-grade design system** that balances professionalism with warmth. The design prioritizes **trust, clarity, and accessibility** while maintaining a calming aesthetic suitable for healthcare applications.

---

## 🎨 Color Palette

### Primary Colors
```css
--primary-green: #2E7D32        /* Deep Natural Green - Main brand color */
--primary-green-dark: #1B5E20   /* Darker shade for gradients/hover */
--primary-green-light: #4CAF50  /* Lighter accent */
```

### Secondary Colors
```css
--secondary-sage: #A5D6A7       /* Soft Sage - Complementary color */
--secondary-sage-light: #C8E6C9 /* Light sage for backgrounds */
--secondary-sage-dark: #81C784  /* Darker sage for emphasis */
```

### Background Colors
```css
--bg-beige: #FAFAF5            /* Warm Beige - Main background */
--bg-white: #FFFFFF            /* Pure white for cards/modals */
--bg-light-green: #E8F5E9      /* Light green for sections */
```

### Text Colors
```css
--text-primary: #2C3E2D        /* Dark Olive - Primary text */
--text-secondary: #5F6F65      /* Medium gray-green - Secondary text */
--text-muted: #8A9A8F          /* Light gray - Muted text */
--text-white: #FFFFFF          /* White text on dark backgrounds */
```

### Utility Colors
```css
--success: #4CAF50             /* Success states */
--warning: #FFA726             /* Warnings/alerts */
--error: #EF5350               /* Errors/destructive actions */
--info: #42A5F5                /* Informational messages */
```

### Accent
```css
--accent-gold: #D4AF37         /* Gold accent for premium features */
```

---

## 🔤 Typography

### Font Families
```css
/* Headings - Elegant, professional */
font-family: 'Playfair Display', serif;

/* Body Text - Clean, readable */
font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
```

### Heading Sizes
```css
h1 { font-size: 3.5rem; }    /* 56px - Hero headlines */
h2 { font-size: 2.5rem; }    /* 40px - Section titles */
h3 { font-size: 2rem; }      /* 32px - Subsection titles */
h4 { font-size: 1.5rem; }    /* 24px - Card titles */
h5 { font-size: 1.25rem; }   /* 20px - Small headings */
h6 { font-size: 1rem; }      /* 16px - Tiny headings */
```

### Body Text
```css
body { font-size: 16px; line-height: 1.6; }
p { font-size: 1rem; line-height: 1.7; }
```

---

## 🔘 Button Styles

### Primary Button
```css
.btn-primary {
    background: linear-gradient(135deg, #2E7D32, #1B5E20);
    color: #FFFFFF;
    padding: 0.875rem 1.75rem;
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(46, 125, 50, 0.25);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(46, 125, 50, 0.35);
}
```

**Usage:** Primary actions (Sign Up, Submit, Save)

### Secondary Button
```css
.btn-secondary {
    background: #FFFFFF;
    color: #2E7D32;
    border: 2px solid #2E7D32;
    padding: 0.875rem 1.75rem;
    border-radius: 10px;
}

.btn-secondary:hover {
    background: #E8F5E9;
}
```

**Usage:** Secondary actions (Cancel, Learn More)

### Outline Button
```css
.btn-outline {
    background: transparent;
    color: #2E7D32;
    border: 2px solid #A5D6A7;
    padding: 0.875rem 1.75rem;
    border-radius: 10px;
}

.btn-outline:hover {
    background: #E8F5E9;
    border-color: #2E7D32;
}
```

**Usage:** Tertiary actions (View Details, Explore)

---

## 🃏 Card Styles

### Standard Card
```css
.card {
    background: #FFFFFF;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(46, 125, 50, 0.08);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 16px rgba(46, 125, 50, 0.12);
    transform: translateY(-2px);
}
```

**Usage:** Content containers, feature cards, stat cards

---

## 📝 Input Field Styles

### Text Input
```css
.input-field {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid #E0E7E4;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.input-field:focus {
    outline: none;
    border-color: #2E7D32;
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
}

.input-field::placeholder {
    color: #8A9A8F;
}
```

**Usage:** Forms, search bars, data entry

---

## 🎭 Shadows

```css
--shadow-sm: 0 2px 8px rgba(46, 125, 50, 0.08);    /* Subtle elevation */
--shadow-md: 0 4px 16px rgba(46, 125, 50, 0.12);   /* Medium elevation */
--shadow-lg: 0 8px 32px rgba(46, 125, 50, 0.16);   /* High elevation */
```

---

## 📏 Border Radius

```css
--radius-sm: 6px;     /* Small elements (badges, tags) */
--radius-md: 10px;    /* Medium elements (buttons, inputs) */
--radius-lg: 16px;    /* Large elements (cards) */
--radius-xl: 24px;    /* Extra large (hero images, modals) */
```

---

## 📐 Spacing System

```css
--spacing-xs: 0.5rem;   /* 8px */
--spacing-sm: 1rem;     /* 16px */
--spacing-md: 1.5rem;   /* 24px */
--spacing-lg: 2rem;     /* 32px */
--spacing-xl: 3rem;     /* 48px */
```

---

## ✨ Hover & Focus States

### Hover States
- **Buttons:** Lift effect (`translateY(-2px)`) + enhanced shadow
- **Cards:** Subtle lift + shadow increase
- **Links:** Color change to primary green

### Focus States
- **Inputs:** Green border + subtle green glow
- **Buttons:** Maintain hover state + outline for accessibility
- **Links:** Visible focus ring for keyboard navigation

---

## 📱 Responsive Breakpoints

```css
/* Mobile */
@media (max-width: 480px) { ... }

/* Tablet */
@media (max-width: 768px) { ... }

/* Desktop */
@media (max-width: 968px) { ... }

/* Large Desktop */
@media (min-width: 1200px) { ... }
```

---

## ♿ Accessibility Guidelines

1. **Color Contrast:** All text meets WCAG AA standards (4.5:1 minimum)
2. **Focus Indicators:** Visible focus states on all interactive elements
3. **Semantic HTML:** Proper heading hierarchy and ARIA labels
4. **Keyboard Navigation:** All features accessible via keyboard
5. **Alt Text:** Descriptive alt text for all images

---

## 🎯 Usage Examples

### Example: Stat Card
```html
<div class="card">
    <h4>Active Conditions</h4>
    <p class="stat-number">3</p>
    <p class="stat-label">Being monitored</p>
</div>
```

### Example: Primary CTA
```html
<a href="register.php" class="btn btn-primary">
    <i class="fas fa-user-plus"></i> Get Started
</a>
```

### Example: Form Input
```html
<input type="email" 
       class="input-field" 
       placeholder="Enter your email"
       required>
```

---

## 📋 Component Checklist

When creating new components, ensure:
- [ ] Uses design system colors
- [ ] Follows spacing guidelines
- [ ] Includes hover/focus states
- [ ] Is mobile responsive
- [ ] Meets accessibility standards
- [ ] Uses consistent border radius
- [ ] Applies appropriate shadows

---

**Version:** 1.0  
**Last Updated:** January 2026  
**Maintained by:** VitalBalance Development Team
