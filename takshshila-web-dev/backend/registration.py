import re #module to check email format
import mysql.connector as m #module to connect to MySQL database

con = m.connect(host='localhost', user='root', password='', database='userdata') # Connect to MySQL database
cur = con.cursor() # Create a cursor object to execute SQL queries


def is_valid_email(email): # Function to validate email format
        # Simple regex for email validation
        pattern = r'^[\w\.-]+@[\w\.-]+\.\w+$'
        return re.match(pattern, email) is not None

# List to store registered usernames
db_users = []
cur.execute("SELECT username FROM pass")

for (username,) in cur.fetchall(): # Fetch all usernames from the database
    db_users.append(username) # Append each username to the list

# This script allows a user to register
while True:
    first_name = input("Enter first name: ")
    last_name = input("Enter last name: ")
    ph_number = int(input("Enter phone number: "))
    email = input("Enter email: ")
    username = input("Enter username: ")
    password = input("Enter password: ")
    confirm_password = input("Confirm password: ")
    
    # Validate email format
    if is_valid_email(email):
        print("Valid email ID.")
    else:
        print("Invalid email ID.")
        continue

    # Validate phone number
    if len(str(ph_number)) != 10:
        print("Phone number must be 10 digits long.")
        continue
    
    # Check if username already exists
    if username in db_users:
        print("Username already exists. Please try another.")
        continue

    # Check if passwords match
    if password != confirm_password:
        print("Passwords do not match. Please try again.")
        continue

    #check whether any field is empty
    if not first_name or not last_name or not email or not username or not password:
        print("All fields are required. Please fill in all details.")
        continue

    break

data = (username, first_name, last_name, ph_number, email)
passkey = (username, password)

# Insert user data into the database
cur.execute("INSERT INTO data (username, first_name, last_name, phone_number, email) VALUES (%s, %s, %s, %s, %s)", data)
cur.commit()

# Insert password into the database
cur.execute("INSERT INTO pass (username, password) VALUES (%s, %s)", passkey)
cur.commit()

print("Registration successful!")
