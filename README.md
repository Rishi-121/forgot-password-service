# _Forgot Password Service_

Step-1: Import the below sql command for ```forgot_password_email``` table.

```sql
CREATE TABLE `forgot_password_emails` (
  `index_number` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `time` datetime NOT NULL,
  `count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
```

Step-2: Create the user table with column id, username , emailid.

Step-3: Run the application
