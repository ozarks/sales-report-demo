📊 Sales Report Demo — Laravel Mini Application

This Laravel mini-application generates a Product Order Summary Report with:

Summary metrics (Total Orders, Total Revenue, Top 3 Best-Selling Products, Average Order Value)

Detailed Order Table (Orders + Customers + Products + Categories)

Excel export including merged summary cells + styled detailed table

Optimized performance (NO N+1 queries)

🚀 Installation Guide
1. Clone the Project
git clone <your-repository-url>
cd sales-report-demo

🐳 Option A — Run Using Docker (Recommended)
2. Start Docker Containers
docker compose up -d --build

3. Enter PHP Container
docker exec -it sales-report-app bash

4. Install Dependencies
composer install

5. Create Environment File
cp .env.example .env
php artisan key:generate

6. Run Migration + Seed Data
php artisan migrate --seed

7. Access the Application
http://localhost:8001/report

💻 Option B — Run Locally Without Docker
2. Install Dependencies
composer install

3. Configure Environment
cp .env.example .env
php artisan key:generate


Set DB connection in .env.

4. Migrate + Seed Data
php artisan migrate --seed

5. Start Local Development Server
php artisan serve


Access the app:

http://localhost:8000/report

🗄️ Database Schema & Seeding

The system includes the following tables:

Table	Columns
customers	id, name, email, state
categories	id, name
products	id, name, category_id, price
orders	id, customer_id, order_date, total_amount
order_items	id, order_id, product_id, quantity, unit_price
Relationships:

Customer → hasMany Orders

Order → belongsTo Customer

Order → hasMany OrderItems

OrderItem → belongsTo Product

Product → belongsTo Category

Seed Data

Running:

php artisan migrate:fresh --seed


will generate:

10+ customers

3+ categories

10+ products

sample orders

sample order items

📘 Report Features

The /report page contains:

A. Summary Section

Each metric is computed using a separate optimized query, as required:

Total number of orders

Total revenue

Top 3 best-selling products

Average order value

B. Detailed Table

Columns include:

| Order Date | Customer | State | Category | Product | Qty | Unit Price | Subtotal |

All data is retrieved using joined queries + eager loading, ensuring no N+1.

📤 Excel Export Feature

Click Download Excel to export:

✔ Summary Section (merged cells)

Displayed at the top of the Excel sheet with:

Bold headers

Borders

Merged cells

Background styling

✔ Detailed Table

Matching the report page table:

Styled headers

Numeric alignment

Borders

Per-order grouping & totals

✔ Implementation Notes

Export built using HTML-based XLS (no external PHP Excel libraries required)

Excel may show:

“The file format and extension don’t match”

This is normal for HTML-generated XLS.
Click Yes, and the file opens fully formatted.

🧠 Technical Notes / Assumptions

The export uses HTML tables served as Excel-compatible .xls files for simplicity.

No external Excel library (PhpSpreadsheet / Laravel Excel) is used.

All queries avoid N+1 by using:

Order::with(['customer', 'items.product.category'])->get();


Column names match assignment spec:

customers.state

orders.total_amount

The main report route is /report.

✔️ Assignment Requirement Checklist
Requirement	Status
Customers / Categories / Products / Orders / Order Items tables	✅ Done
Relationships implemented	✅ Done
Summary section with 4 metrics	✅ Done
Detailed table with required columns	✅ Done
/report page created	✅ Done
Download Excel button	✅ Done
Excel summary section with merged cells	✅ Done
Styled Excel table (bold, borders, alignment)	✅ Done
No N+1 queries	✅ Done
README with installation + notes	✅ Done