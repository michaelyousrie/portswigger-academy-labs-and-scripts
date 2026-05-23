# Redeemer

[Redeemer Redis Machine](https://app.hackthebox.com/machines/Redeemer)

This machine is about connecting to a redis database and fetching the flag value from it. It's basically about using a few basic Redis commands;

- `keys *` which lists all the keys in a given database
- `get [Key_Name]` which gets the value of a given key.

# Machine Tasks (Questions)

> Which TCP port is open on the machine?
>> 6379

> Which service is running on the port that is open on the machine?
>> redis

> What type of database is Redis? Choose from the following options: (i) In-memory Database, (ii) Traditional Database
>> In-memory Database

> Which command-line utility is used to interact with the Redis server? Enter the program name you would enter into the terminal without any arguments.
>> redis-cli

> Which flag is used with the Redis command-line utility to specify the hostname?
>> -h

> Once connected to a Redis server, which command is used to obtain the information and statistics about the Redis server?
>> info

> What is the version of the Redis server being used on the target machine?
>> 5.0.7

> Which command is used to select the desired database in Redis?
>> select


> How many keys are present inside the database with index 0?
>> 4


> Which command is used to obtain all the keys in a database?
>> keys *

# Get the flag value

To get the flag, you simply just have to do `get flag` on Redis CLI and you'll get the value right there.

# Tools

You need to install `redis-cli` by using
```bash
sudo apt install redis-cli
```
Then you can connect to the machine using
```bash
redis-cli -h [MACHINE_IP]
```

That's it really. Once you're connected, just do `select 0` and then `get flag` and you'll have your flag!