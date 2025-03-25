<?php




require_once "conexion.php";



class Servidor {
    private $db;

    public function __construct() {
        $this->db = (new Conexion())->getConexion();
    }


    public function autenticar($token) {
        $tokenCorrecto = "12345";
    
        if ($token === $tokenCorrecto) {
            return true;
        } else {
            return false;
        }
    }
    


    public function Login($usuario, $contraseña, $token)
    {
        if (!$this->autenticar($token)) {
            return "Token inválido";
        }
    
        $stmt = $this->db->prepare("SELECT * FROM tbl_users WHERE User = ?");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user) {
            if (password_verify($contraseña, $user['Password'])) {
                    return "Login exitoso";
            } else {
                return "Usuario o contraseña incorrectos";
            }
        } else {
            return "Usuario o contraseña incorrectos";
        }
    }
    

    public function ObtenerUsuario($token) 
    {
        if (!$this->autenticar($token)) {
            return "Token inválido";
        }

    
        $stmt = $this->db->prepare("SELECT idUser, Name, User FROM tbl_users");
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $xml = new SimpleXMLElement('<usuarios/>');
    
        foreach ($usuarios as $usuario) {
            $usuarioXml = $xml->addChild('usuario');
            $usuarioXml->addChild('idUser', htmlspecialchars($usuario['idUser']));
            $usuarioXml->addChild('Name', htmlspecialchars($usuario['Name']));
            $usuarioXml->addChild('User', htmlspecialchars($usuario['User']));
        }
    
        header("Content-Type: text/xml; charset=UTF-8");
        echo $xml->asXML();
        exit;
    }

    public function CrearUsuario($nombre, $usuario, $contraseña, $token)
    {
        if (!$this->autenticar($token)) {
            return "Error: Token inválido"; 
        }
    
        try {
            $hash = password_hash($contraseña, PASSWORD_BCRYPT);
    
            $stmt = $this->db->prepare("INSERT INTO tbl_users (User, Name, Password) VALUES (?, ?, ?)");
            $stmt->execute([$usuario, $nombre, $hash]);
    
            if ($stmt->rowCount() > 0) {
                return "Usuario creado exitosamente";
            } else {
                return "Error: No se pudo crear el usuario";
            }
        } catch (PDOException $e) {
            return "Error en la base de datos: " . $e->getMessage();
        }
    }

    public function ActualizarUsuario($id, $nombre, $usuario, $contraseña, $token)
    {
        if (!$this->autenticar($token)) {
            return "Error: Token inválido";
        }
    
        if (!empty($contraseña)) {
            $contraseña = password_hash($contraseña, PASSWORD_BCRYPT);
            $sql = "UPDATE tbl_users SET Name = ?, User = ?, Password = ? WHERE idUser = ?";
            $params = [$nombre, $usuario, $contraseña, $id];
        } else {
            $sql = "UPDATE tbl_users SET Name = ?, User = ? WHERE idUser = ?";
            $params = [$nombre, $usuario, $id];
        }
    
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->rowCount() > 0 ? "Usuario actualizado exitosamente" : "Error: No se realizaron cambios";
        } catch (PDOException $e) {
            return "Error en la base de datos: " . $e->getMessage();
        }
    }
    
    
    

    public function EliminarUsuario($id, $token)
    {
        if (!$this->autenticar($token)) {
            return "Token inválido";
        }
    
        try {
            $stmt = $this->db->prepare("DELETE FROM tbl_users WHERE idUser = ?");
            $stmt->execute([$id]);
    
            if ($stmt->rowCount() > 0) {
                return "Usuario eliminado exitosamente";
            } else {
                return "No se encontró el usuario o ya fue eliminado";
            }
        } catch (PDOException $e) {
            return "Error al eliminar el usuario: " . $e->getMessage();
        }
    }
    

}   