import mysql.connector
import bcrypt as bc

counter = 0

def password_checking(password, hashed_password):
    if bc.checkpw(password.encode('utf-8'), hashed_password):
        return True
    else:
        return False

# Connect to MySQL
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="userdata"
)
cursor = conn.cursor()

username = input("Enter username: ")
password = input("Enter password: ")

# Fetch password for the given username
cursor.execute("SELECT pass FROM pass WHERE username = %s", (username,))
hashed_password = cursor.fetchone()

#verify the password
result = password_checking(password, hashed_password[0])

if hashed_password:
    if result == True:
        print("Login successful!")
        counter = 1
    else:
        print("Incorrect password.")
else:
    print("Username not found.")

cursor.close()
conn.close()
