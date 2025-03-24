<?php




require_once "conexion.php";



class Servidor {
    private $db;

    public function __construct() {
        $this->db = (new Conexion())->getConexion();
    }


    public function autenticar($token) {
        $tokenCorrecto = "12345"; // Token fijo
    
        if ($token === $tokenCorrecto) {
            return true; // Autenticación exitosa
        } else {
            return false; // Token inválido
        }
    }
    


    public function Login($usuario, $contraseña, $token)
    {
        // Verificar autenticación con el token
        if (!$this->autenticar($token)) {
            return "Token inválido";
        }
    
        // Consultar al usuario por su nombre de usuario
        $stmt = $this->db->prepare("SELECT * FROM tbl_users WHERE User = ?");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Verificar si el usuario existe
        if ($user) {
            // Verificar la contraseña usando password_verify
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
        // Verificación del token

        if (!$this->autenticar($token)) {
            return "Token inválido";
        }

    
        $stmt = $this->db->prepare("SELECT idUser, Name, User FROM tbl_users");
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Construcción del XML
        $xml = new SimpleXMLElement('<usuarios/>');
    
        foreach ($usuarios as $usuario) {
            $usuarioXml = $xml->addChild('usuario');
            $usuarioXml->addChild('idUser', htmlspecialchars($usuario['idUser']));
            $usuarioXml->addChild('Name', htmlspecialchars($usuario['Name']));
            $usuarioXml->addChild('User', htmlspecialchars($usuario['User']));
        }
    
        // Enviar encabezado XML y devolver respuesta
        header("Content-Type: text/xml; charset=UTF-8");
        echo $xml->asXML();
        exit;
    }

    public function CrearUsuario($nombre, $usuario, $contraseña, $token)
    {
        // Verificar autenticación
        if (!$this->autenticar($token)) {
            return "Error: Token inválido"; // 🔹 Devuelve un string simple
        }
    
        try {
            $hash = password_hash($contraseña, PASSWORD_BCRYPT);
    
            $stmt = $this->db->prepare("INSERT INTO tbl_users (User, Name, Password) VALUES (?, ?, ?)");
            $stmt->execute([$usuario, $nombre, $hash]);
    
            if ($stmt->rowCount() > 0) {
                return "Usuario creado exitosamente"; // 🔹 String en lugar de JSON
            } else {
                return "Error: No se pudo crear el usuario";
            }
        } catch (PDOException $e) {
            return "Error en la base de datos: " . $e->getMessage();
        }
    }

    public function ActualizarUsuario($id, $nombre, $usuario, $contraseña, $token)
    {
        // Verificar autenticación
        if (!$this->autenticar($token)) {
            return "Error: Token inválido";
        }
    
        // Preparar la consulta
        if (!empty($contraseña)) {
            $contraseña = password_hash($contraseña, PASSWORD_BCRYPT);
            $sql = "UPDATE tbl_users SET Name = ?, User = ?, Password = ? WHERE idUser = ?";
            $params = [$nombre, $usuario, $contraseña, $id];
        } else {
            $sql = "UPDATE tbl_users SET Name = ?, User = ? WHERE idUser = ?";
            $params = [$nombre, $usuario, $id];
        }
    
        // Ejecutar la consulta
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
        // Verificar autenticación
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
    


/*
    public function obtenerProductos($token) {
        if (!$this->autenticar($token)) {
            return [];
        }

        $query = $this->db->query("SELECT id, nombre, precio, stock FROM productos");
        $productos = $query->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($producto) {
            return [
                'id' => (int) $producto['id'],
                'nombre' => $producto['nombre'],
                'precio' => (float) $producto['precio'],
                'stock' => (int) $producto['stock']
            ];
        }, $productos);
    }

    public function obtenerProducto($id, $token) {
        if (!$this->autenticar($token)) {
            return [];
        }

        $stmt = $this->db->prepare("SELECT id, nombre, precio, stock FROM productos WHERE id = ?");
        $stmt->execute([$id]);

        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            return [];
        }

        return [
            'id' => (int) $producto['id'],
            'nombre' => $producto['nombre'],
            'precio' => (float) $producto['precio'],
            'stock' => (int) $producto['stock']
        ];
        
    }


    public function AddProducto($nombre,$precio,$stock,$token) 
    {
        if (!$this->autenticar($token)) {
            return [];
        }

        $query = $this->db->prepare('INSERT INTO PRODUCTOS (nombre,precio,stock) VALUES(?,?,?)');
        $query->execute([$nombre,$precio,$stock]);

        $ultimoid = $this->db->lastInsertId();


        $query1 = $this->db->prepare('SELECT id,nombre,precio,stock FROM PRODUCTOS WHERE id=?');
        $query1->execute([$ultimoid]);
        
        $producto = $query1->fetch(PDO::FETCH_ASSOC);


        if(!$producto){
            return [];
        }

        return  [

            'id'=> (int) $producto['id'],   
            'nombre'=> $producto['nombre'],
            'precio'=> (float) $producto['precio'],
            'stock'=> (int) $producto['stock'],
        ];
    }

    public function UpdateProducto($id,$nombre,$precio,$stock,$token)
    {
        if (!$this->autenticar($token)) {   
        return [];
        }

        $query = $this->db->prepare('UPDATE PRODUCTOS SET nombre=?,precio=?,stock=? WHERE id=?');
        $query->execute([$nombre,$precio,$stock,$id]);

        
        $query1 = $this->db->prepare('SELECT id,nombre,precio,stock FROM PRODUCTOS  WHERE id=?');
        $query1->execute([$id]);

        $producto = $query1->fetch(PDO::FETCH_ASSOC);

        if(!$producto){ 
            return [];
        }

        return [
            'id'=> (int) $producto['id'],
            'nombre'=> $producto['nombre'],
            'precio'=> (float) $producto['precio'],
            'stock'=> (int) $producto['stock']
        ];

    }
    public function DeleteProducto($id, $token)
    {
        if (!$this->autenticar($token)) {  
            return [];
        }
    
        $query1 = $this->db->prepare('SELECT id, nombre, precio, stock FROM PRODUCTOS WHERE id = ?');
        $query1->execute([$id]);
        
        $resultado = $query1->fetch(PDO::FETCH_ASSOC);
    
        if (!$resultado) {
            return [];
        }
    
        // Almacenar la información del producto antes de eliminarlo
        $producto = [
            'id' => (int) $resultado['id'],
            'nombre' => $resultado['nombre'],
            'precio' => (float) $resultado['precio'],
            'stock' => (int) $resultado['stock']
        ];
    
        // Eliminar el producto
        $sql = $this->db->prepare('DELETE FROM PRODUCTOS WHERE id = ?');
        $sql->execute([$id]);
    
        // Devolver la información del producto eliminado
        return $producto;
    }
    
}
echo password_hash("mi_token_secreto", PASSWORD_BCRYPT);
*/


}
?>