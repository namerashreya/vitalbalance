# How to Run insert_diseases.sql in XAMPP

## Method 1: Using phpMyAdmin (Recommended)

### Step 1: Start XAMPP Services
1. Open **XAMPP Control Panel**
2. Start **Apache** (if not already running)
3. Start **MySQL** (if not already running)

### Step 2: Access phpMyAdmin
1. Open your web browser
2. Go to: `http://localhost/phpmyadmin`
3. You should see the phpMyAdmin interface

### Step 3: Select Your Database
1. In the left sidebar, click on your database name (e.g., `vitalbalance_db` or whatever your database is named)
2. If you're not sure of the database name, check your `includes/db.php` file

### Step 4: Import the SQL File
1. Click on the **"Import"** tab at the top of the page
2. Click **"Choose File"** or **"Browse"** button
3. Navigate to your project folder: `C:\xampp\htdocs\vitalbalance_db\`
4. Select the file: `insert_diseases.sql`
5. Make sure **"SQL"** format is selected
6. Click **"Go"** or **"Import"** button at the bottom
7. Wait for the import to complete - you should see a success message

### Step 5: Verify the Import
1. Click on the **"diseases"** table in the left sidebar
2. Click on the **"Browse"** tab
3. You should see all 50+ diseases listed
4. You can also check the count - it should show many rows

---

## Method 2: Using SQL Tab (Copy & Paste)

### Step 1-3: Same as Method 1

### Step 4: Copy and Paste SQL
1. Click on the **"SQL"** tab at the top
2. Open `insert_diseases.sql` file in a text editor (Notepad, VS Code, etc.)
3. Copy **ALL** the content from the SQL file (Ctrl+A, then Ctrl+C)
4. Paste it into the SQL text area in phpMyAdmin (Ctrl+V)
5. Click **"Go"** button
6. Wait for execution - you should see success messages

---

## Method 3: Using MySQL Command Line

### Step 1: Open Command Prompt
1. Press `Win + R`
2. Type `cmd` and press Enter

### Step 2: Navigate to MySQL
1. Type: `cd C:\xampp\mysql\bin`
2. Press Enter

### Step 3: Run the SQL File
1. Type the following command (replace `your_database_name` with your actual database name):
   ```
   mysql -u root -p your_database_name < C:\xampp\htdocs\vitalbalance_db\insert_diseases.sql
   ```
2. Press Enter
3. Enter your MySQL password when prompted (default is usually empty, just press Enter)
4. Wait for the import to complete

---

## Troubleshooting

### Issue: "Table doesn't exist"
- **Solution**: Make sure you've run the main database schema first (`database_schema_v2.sql`)

### Issue: "Duplicate entry" errors
- **Solution**: Some diseases might already exist. The SQL will skip duplicates or you can delete existing diseases first:
  ```sql
  DELETE FROM diseases;
  ```
  Then run the import again.

### Issue: "Access denied"
- **Solution**: Check your database credentials in `includes/db.php` and make sure MySQL is running

### Issue: File not found
- **Solution**: Make sure the file path is correct: `C:\xampp\htdocs\vitalbalance_db\insert_diseases.sql`

---

## Quick Check After Import

Run this SQL query in phpMyAdmin SQL tab to verify:
```sql
SELECT COUNT(*) as total_diseases FROM diseases;
SELECT category, COUNT(*) as count FROM diseases GROUP BY category ORDER BY category;
```

You should see:
- Total diseases: 50+ 
- Multiple categories listed with their counts

---

## Need Help?

If you encounter any issues:
1. Check that MySQL is running in XAMPP Control Panel
2. Verify your database name matches what's in `includes/db.php`
3. Make sure the `diseases` table exists (run `database_schema_v2.sql` first if needed)
