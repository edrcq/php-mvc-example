<?php

require_once('database.php');

class User
{
    protected $id;
    protected $email;
    protected $username;
    protected $password;
    protected $avatar_url;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = hash("sha256", $password);
    }

    /**
     * @return mixed
     */
    public function getAvatarUrl()
    {
        return $this->avatar_url;
    }

    /**
     * @param mixed $avatar_url
     */
    public function setAvatarUrl($avatar_url)
    {
        $this->avatar_url = $avatar_url;
    }


    /**************************************
     * -------- GET USER DATA BY ID --------
     ***************************************/

    public static function getUserById($id)
    {
        // Open database connection
        $db = init_db();

        $req = $db->prepare("SELECT * FROM users WHERE id = ?");
        $req->execute([$id]);

        // Close database connection
        $db = null;

        return $req->fetch();
    }

    /**************************************
     * -------- GET USER DATA BY ID --------
     ***************************************/

    public static function getUserByEmail($email)
    {
        // Open database connection
        $db = init_db();

        $req = $db->prepare("SELECT * FROM users WHERE email = ?");
        $req->execute([$email]);

        // Close database connection
        $db = null;

        return $req->fetch(PDO::FETCH_CLASS, "User");
    }

    /***************************************
     * ------- GET USER DATA BY USERNAME -------
     ****************************************/

    public static function getUserByCredentials($email, $password)
    {
        // Open database connection
        $db = init_db();

        $req = $db->prepare("SELECT * FROM users WHERE email=? AND password=?");
        $req->execute([
            $email,
            $password
        ]);

        // Close database connection
        $db = null;

        return $req->fetch();
    }

    public static function getFriendsForUser($user_id): array
    {
        // Open database connection
        $db = init_db();

        $req = $db->prepare("SELECT users.* FROM users LEFT JOIN friends ON users.id = friends.friend_user_id WHERE friends.user_id = ?");
        $req->execute([$user_id]);

        // Close database connection
        $db = null;

        return $req->fetchAll();
    }

    public static function findUserWithUsername($username)
    {
        // Open database connection
        $db = init_db();

        $req = $db->prepare("SELECT * FROM users WHERE username = ?");
        $req->execute([$username]);

        // Close database connection
        $db = null;

        return $req->fetch();
    }


    public static function isAlreadyFriend($user_id, $friend_id)
    {
        // Open database connection
        $db = init_db();

        $req = $db->prepare("SELECT COUNT(*) FROM friends WHERE (user_id = ? AND friend_user_id = ?) OR (user_id = ? AND friend_user_id = ?)");
        $req->execute([
            $user_id,
            $friend_id,
            $friend_id,
            $user_id
        ]);

        $isAlreadyFriend = $req->fetchColumn() > 0;

        // Close database connection
        $db = null;

        return $isAlreadyFriend;
    }

    public static function addFriend($user_id, $friend_id)
    {
        // Open database connection
        $db = init_db();

        $req = $db->prepare("INSERT INTO friends VALUES (NULL, ?, ?)");
        $req->execute([
            $user_id,
            $friend_id
        ]);

        $id = $db->lastInsertId();
        // Close database connection
        $db = null;

        return $id;
    }

    /**
     * Take a simple username like `bob`
     * Return a valid and available username like bob#0142
     */
    public static function generateUsername($username) {
        $db = init_db();
        $must_work = true;
        $full_username = "";

        do {
            // generate random username. Easiest way to do 4 number like #0021 or #9432
            $full_username = $username . "#" . random_int(0, 9). random_int(0, 9) . random_int(0, 9) . random_int(0, 9);
            // check if user already exist.
            $user = User::findUserWithUsername($full_username);
            if (!$user) {
                $must_work = false;
            }
        } while($must_work);

        return $full_username;
    }

    /**
     * Used to register an user
     */
    public static function createUser($obj_user) {
        // Open database connection
        $db = init_db();

        $token = bin2hex(random_bytes(20));

        $req = $db->prepare("INSERT INTO users(email, username, password, email_token) VALUES (?, ?, ?, ?)");
        $req->execute([
            $obj_user->email,
            $obj_user->username,
            $obj_user->password,
            $token
        ]);

        $id = $db->lastInsertId();
        // Close database connection
        $db = null;

        return $token;
    }

    public static function verifyEmailToken($token) {
        // Open database connection
        $db = init_db();

        $req = $db->prepare("UPDATE users SET email_token = NULL WHERE email_token = ?");
        $req->execute([
            $token
        ]);

        return $req->rowCount();
    }
}
