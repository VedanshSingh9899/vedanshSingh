import bcrypt as bc

a = "password123"
pass1 = b"password123"

salt = bc.gensalt()
hashed = bc.hashpw(pass1,salt)

print(a)

print("Hashed password:", hashed)

password = hashed+b"12123"

# Check if the password matches
if bc.checkpw(pass1, password):
    print("Password matches")
else:
    print("Password does not match")