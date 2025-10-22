# TaxMindr - Philippine Tax Compliance Platform

## Rationale
TaxMindr was born from personal experiences with tax compliance challenges in the Philippines:
- Long and inefficient TIN ID application processes
- Church penalties due to lack of registered email for e-filing
- Outdated and manual tax procedures

## Problem Statement
1. **Missed Deadlines**: People miss tax deadlines and pay penalties
2. **Complex Rules**: Tax rules change frequently and are hard to understand
3. **Filing Errors**: Errors in withholding lists (TIN/ATC/totals) cause rework and risk
4. **Multiple Taxes**: Small businesses and freelancers juggle multiple tax types:
   - Income Tax
   - VAT or Percentage Tax
   - Withholding—Expanded
   - Withholding—Compensation

## Core Features

### 1. Deadline Calendar & Reminders
- Auto-generate dates based on user profile
- Email and SMS notifications
- Mark Filed/Paid status
- Activity log tracking

### 2. Tax Updates with Actions
- Short, digestible summaries
- Who is affected
- Action items
- Links to official BIR sources

### 3. Withholding List Upload & Check
- Easy Excel/CSV template support
- Validation for:
  - Wrong TINs
  - Missing ATC codes
  - Incorrect totals
- Monthly archive system
- Clean downloadable reports

### 4. Final Check Before Submit
- Critical error flagging
- Requires fixes before submission
- Receipt/screenshot upload requirement
- Submission confirmation

### 5. Tax Reports & Archive
- Generate exportable tax reports
- Secure storage of past filings
- Searchable archive
- Compliance-ready documentation

## Target Users
- Freelancers
- Small business owners
- Church accounting teams
- Individual taxpayers
- Accounting professionals

## Tech Stack
- **Backend**: PHP
- **Database**: MySQL (via XAMPP)
- **Frontend**: HTML, CSS, JavaScript
- **Notifications**: Email/SMS integration
- **File Processing**: Excel/CSV parsing
