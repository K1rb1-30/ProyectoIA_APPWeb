---
name: Warm Iberian Modernism
colors:
  surface: '#f9f9ff'
  surface-dim: '#d3daea'
  surface-bright: '#f9f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f0f3ff'
  surface-container: '#e7eefe'
  surface-container-high: '#e2e8f8'
  surface-container-highest: '#dce2f3'
  on-surface: '#151c27'
  on-surface-variant: '#584237'
  inverse-surface: '#2a313d'
  inverse-on-surface: '#ebf1ff'
  outline: '#8c7164'
  outline-variant: '#e0c0b1'
  surface-tint: '#9d4300'
  primary: '#9d4300'
  on-primary: '#ffffff'
  primary-container: '#f97316'
  on-primary-container: '#582200'
  inverse-primary: '#ffb690'
  secondary: '#555f6f'
  on-secondary: '#ffffff'
  secondary-container: '#d6e0f3'
  on-secondary-container: '#596373'
  tertiary: '#006c49'
  on-tertiary: '#ffffff'
  tertiary-container: '#00b07a'
  on-tertiary-container: '#003b26'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffdbca'
  primary-fixed-dim: '#ffb690'
  on-primary-fixed: '#341100'
  on-primary-fixed-variant: '#783200'
  secondary-fixed: '#d9e3f6'
  secondary-fixed-dim: '#bdc7d9'
  on-secondary-fixed: '#121c2a'
  on-secondary-fixed-variant: '#3d4756'
  tertiary-fixed: '#6ffbbe'
  tertiary-fixed-dim: '#4edea3'
  on-tertiary-fixed: '#002113'
  on-tertiary-fixed-variant: '#005236'
  background: '#f9f9ff'
  on-background: '#151c27'
  surface-variant: '#dce2f3'
typography:
  display-hero:
    fontFamily: Inter
    fontSize: 40px
    fontWeight: '800'
    lineHeight: 48px
    letterSpacing: -0.02em
  display-hero-mobile:
    fontFamily: Inter
    fontSize: 30px
    fontWeight: '800'
    lineHeight: 36px
    letterSpacing: -0.015em
  headline-lg:
    fontFamily: Inter
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 34px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Inter
    fontSize: 22px
    fontWeight: '700'
    lineHeight: 28px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-lg:
    fontFamily: Inter
    fontSize: 15px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.01em
  label-md:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '600'
    lineHeight: 18px
    letterSpacing: 0.01em
  label-sm:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '700'
    lineHeight: 14px
    letterSpacing: 0.03em
  price-display:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '700'
    lineHeight: 22px
    letterSpacing: -0.01em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-md: 1.5rem
  margin: 1rem
  margin-md: 1.5rem
  margin-lg: 2.5rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2rem
---

## Brand & Style
The design system reflects the warmth, conviviality, and efficiency of contemporary Spanish gastronomy. It merges modern culinary culture with seamless digital table service.

- **Brand Personality**: Hospitable, energetic, lucid, and dependable. The visual tone mirrors a buzzing tapas bar curated with Scandinavian clarity—warm light, terracotta, and crisp ceramics.
- **Target Audience**: Diners ordering at the table, venue waitstaff monitoring fulfillment, and kitchen operators managing real-time orders.
- **Design Movement**: Warm Minimalist Tactile. Flat, airy foundational surfaces balanced by substantial, physical touch targets, deliberate warm micro-shadows, and unmistakable state signaling.
- **Emotional Response**: Appetite-stimulating reassurance, speed, and effortless comfort under variable lighting conditions.

## Colors
The palette grounds vibrant citrus warmth within deep stone and culinary status tones.

- **Primary (`#F97316`)**: Represents brand vitality, primary call-to-actions, cart badges, and order completion flows.
- **Secondary (`#1F2937`)**: Dark charcoal slate used for high-contrast headlines, primary button text when inverted, and structural navigational anchors.
- **Tertiary & Status Accents**:
  - `Ready / Paid` (`#10B981`): Success, bill settled, dish ready for pickup.
  - `Pending / Waiting` (`#F59E0B`): Server call, payment requested, awaiting kitchen confirmation.
  - `Preparing` (`#3B82F6`): Kitchen active state, bar ticket in progress.
- **Neutral Stone Scale**:
  - Base canvas: `#FAFAF9` (warm alabaster).
  - Elevated surfaces: `#FFFFFF` (crisp ceramic white).
  - Borders & Dividers: `#E7E5E4` (soft stone).
  - Subdued / Secondary text: `#6B7280` (balanced slate).

## Typography
Typographic treatment utilizes Inter across all weights to deliver maximum scanning speed in low-light and high-movement restaurant settings.

- **Price Formatting**: Prices always appear with a comma decimal delimiter, a non-breaking space, and the Euro sign (e.g., `12,50 €`). Numbers use tabular figures (`font-variant-numeric: tabular-nums`) to ensure vertical visual alignment in itemized receipts and order cards.
- **Hierarchy Strategy**: Section headings pair heavy weights (`700`/`800`) with tighter tracking, creating clear architectural breaks across long dining menus.

## Layout & Spacing
A fluid layout constrained to a maximum reading container width of 720px on mobile guest experiences, expanding to a responsive multi-column layout on staff tablet terminals.

- **Breakpoints**:
  - `Mobile` (< 768px): Single-column, edge-to-edge card flow with `1rem` outer canvas margin. Bottom-sheet navigation and anchored sticky CTAs.
  - `Tablet / POS` (768px - 1024px): 8-column layout with split-screen split: menu browser (5 cols) and sticky table order rail (3 cols).
  - `Desktop / Station` (> 1024px): 12-column fixed grid with max-width `1280px`.
- **Rhythm**: All interactive target bounds maintain a strict 8px vertical grid. Spacing between menu items is locked to `space-md` (16px) to avoid selection errors during quick scrolling.

## Elevation & Depth
Depth mimics physical serving ware resting over pale wooden tables. Visual separation relies on delicate ambient light rather than harsh structural borders.

- **Level 0 (Canvas)**: Background surface in `#FAFAF9`. No elevation.
- **Level 1 (Menu & Food Cards)**: Pure white `#FFFFFF` surface with an ambient shadow: `0 2px 8px -2px rgba(31, 41, 55, 0.05), 0 1px 3px 0 rgba(31, 41, 55, 0.03)` alongside a subtle `1px` inner border tint of `#E7E5E4`.
- **Level 2 (Active Sheets, Sticky Bottom Bars, Modals)**: Lifted surface elevated by `0 12px 24px -6px rgba(31, 41, 55, 0.12), 0 4px 8px -2px rgba(31, 41, 55, 0.04)`.
- **Active / Pressed Elements**: Buttons depress physically on `:active` with an internal inset tint and a transform drop of `translateY(1px)`.

## Shapes
Forms prioritize organic, approachable curvature referencing Mediterranean ceramics.

- **Base Radius (`rounded-2xl` equivalent)**: Applied to all product cards, modal sheets, and food photography frames (16px base / `1rem`).
- **Interactive Targets**: Buttons and text inputs use soft pill-adjacent curvature (12px to full-round for tags).
- **Allergen Badges & Status Indicators**: Fully rounded capsules (`9999px`) to immediately distinguish informational status from tap-oriented functional cards.

## Components

### Buttons
- **Touch Standard**: All primary interactive touch targets maintain a minimum vertical height of 48px (`min-height: 48px`).
- **Primary Action**: Background `#F97316`, text `#FFFFFF`, font-weight 600. High tactile feedback with micro-scale press transitions.
- **Secondary Action**: Background `#FFFFFF`, border `1.5px solid #E7E5E4`, text `#1F2937`.
- **Floating Cart Button**: Anchored full-width bottom bar (minus padding), containing total order tally, live badge count, and localized checkout value.

### Menu Item Cards
- Surface `#FFFFFF`, rounded corners `rounded-2xl`, internal padding `space-md`.
- Layout: Horizontal split with item title, description (`#6B7280`), allergen badges, and price (`#1F2937`) on the left, paired with an 88x88px rounded square image and quick-add (`+`) floating trigger on the right.

### Chips & Category Navigation
- Horizontally scrollable pill tabs (`min-height: 40px`).
- Inactive state: `#FAFAF9` with `#6B7280` text.
- Active state: `#1F2937` with `#FFFFFF` text, providing visual contrast against the vibrant primary orange.

### Allergen & Dietary Badges
- Micro-capsules (`padding: 2px 8px`, `font-size: 11px`, `font-weight: 700`).
- Neutral dietary badges (Gluten-Free, Vegan): `#F3F4F6` background, `#374151` text.
- Critical allergen warnings (Nuts, Shellfish): `#FEF2F2` background, `#B91C1C` text.

### Status Indicators
- Table status and kitchen tracking cards feature a solid 4px left-side accent bar matching status colors:
  - Ready/Paid: `#10B981`
  - Pending/Waiting: `#F59E0B`
  - Preparing: `#3B82F6`

### Quantity Stepper
- Compact inline counter with 48x48px hit areas for decrement (`-`) and increment (`+`), with large bold counts in `#1F2937`.

### Input Fields & Special Requests
- Minimum height of 48px, background `#FFFFFF`, border `1px solid #E7E5E4`, focused border `2px solid #F97316` with zero outer glow.