<?php
session_start(); // Inicia la sesión

// Verificar si el usuario está logueado
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php'); // Redirige al login si no está logueado
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <title>Dashboard</title>
    <style>
        /* Estilos generales */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            background: #f4f4f4;
        }

        /* Sidebar (ahora con degradado) */
        aside {
            width: 250px;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100vh;
        }

        .menu {
            list-style: none;
        }

        .menu li {
            margin: 20px 0;
        }

        .menu a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .menu a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .logout {
            text-align: center;
            margin-top: auto;
        }

        .logout a {
            background: red;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            display: block;
            text-align: center;
            transition: 0.3s;
        }

        .logout a:hover {
            background: darkred;
        }

        /* Contenido principal (ahora en blanco) */
        .content {
            flex-grow: 1;
            padding: 20px;
            background: white;
            color: #333;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin: 20px;
            overflow: auto;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #ff7e5f;
        }

        /* Estilos de la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #ff7e5f;
            color: white;
        }

        tr:hover {
            background-color: #ffe0d1;
        }

        /* Mensaje de carga */
        .loading {
            font-size: 18px;
            font-weight: bold;
            color: #ff7e5f;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside>
        <nav>
            <ul class="menu">
                <li><a href="#">Usuarios</a></li>
            </ul>
        </nav>
        <div class="logout">
            <a href="logout.php ">Cerrar Sesión</a>
        </div>
    </aside>

    <!-- Contenido principal -->
    <div class="content">
        <h2>Lista de Usuarios</h2>

        <button type="button" class="btn btn-success mt-3 mb-3" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
    + Nuevo Usuario
</button>
        

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody id="usuarios">
                <tr>
                    <td colspan="3" class="loading">Cargando...</td>
                </tr>
            </tbody>
        </table>
    </div>


    <!---       INICIO        Modulo para crar un nuevo usuario -------->

    <div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevoUsuarioLabel">Crear Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="registroForm">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                        <div id="mensajeRegistro" class="alert alert-danger d-none"></div>
                        <button type="submit" class="btn btn-success">Guardar Usuario</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('registroForm').addEventListener('submit', async function(event) {
        event.preventDefault();

        let nombre = document.getElementById('nombre').value;
        let usuario = document.getElementById('usuario').value;
        let password = document.getElementById('password').value;
        let mensajeRegistro = document.getElementById('mensajeRegistro');
        let token = "12345"; // Token de autenticación

        let soapRequest = `
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:miserviciowsdl">
                <soapenv:Header/>
                <soapenv:Body>
                    <urn:Servidor.CrearUsuario>
                        <nombre>${nombre}</nombre>
                        <usuario>${usuario}</usuario>
                        <contraseña>${password}</contraseña>
                        <token>${token}</token>
                    </urn:Servidor.CrearUsuario>
                </soapenv:Body>
            </soapenv:Envelope>
        `;

        try {
            let response = await fetch('http://127.0.0.1/webservices/Proyectowebservices/invocar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'text/xml'
                },
                body: soapRequest
            });

            let textResponse = await response.text();
            console.log("Respuesta SOAP Completa:", textResponse); // <-- IMPRIME RESPUESTA EN CONSOLA

            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(textResponse, "text/xml");

            let resultTag = xmlDoc.getElementsByTagName("return")[0] || xmlDoc.getElementsByTagName("ns1:return")[0];
            let result = resultTag ? resultTag.textContent : "Error desconocido";

            if (result.includes("exitosamente")) {
                alert("Usuario registrado correctamente");
                document.getElementById('registroForm').reset();
                
                window.location.href = "dashboard.php"; // Redirige a la dashboard

                obtenerUsuarios();
            } else {
                mensajeRegistro.textContent = result;
                mensajeRegistro.classList.remove("d-none");
            }
        } catch (error) {
            console.error('Error:', error);
            mensajeRegistro.textContent = "Error al conectar con el servidor.";
            mensajeRegistro.classList.remove("d-none");
        }
    });
    </script>

    <!---       FIN        Modulo para crear un nuevo usuario -------->
    
    <!---       CREAR        Modulo para ACTUALIZAR un nuevo usuario -------->

<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="editarForm">
                    <input type="hidden" id="idUsuario">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombreEditar" value="">
                    </div>
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="usuarioEditar" value="">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Nueva Contraseña (opcional)</label>
                        <input type="password" class="form-control" id="passwordEditar">
                    </div>
                    <div id="mensajeEdicion" class="alert alert-danger d-none"></div>
                    <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Función para cargar los datos del usuario en el modal de edición
    function cargarDatosUsuario(id, nombre, usuario) {
        document.getElementById('idUsuario').value = id;
        document.getElementById('nombreEditar').value = nombre;
        document.getElementById('usuarioEditar').value = usuario;
    }

    // Modificar el evento de los botones de editar para llenar los campos en el modal
    document.getElementById("usuarios").addEventListener("click", function (event) {
        if (event.target.classList.contains("btn-warning")) {
            let fila = event.target.closest("tr");
            let id = fila.cells[0].textContent;
            let nombre = fila.cells[1].textContent;
            let usuario = fila.cells[2].textContent;
            
            // Llamar la función para llenar los campos del modal
            cargarDatosUsuario(id, nombre, usuario);
        }
    });



    document.getElementById('editarForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    // Obtener los valores de los inputs
    let id = document.getElementById('idUsuario').value;
    let nombre = document.getElementById('nombreEditar').value;
    let usuario = document.getElementById('usuarioEditar').value;
    let password = document.getElementById('passwordEditar').value;
    let token = "12345"; // Token de autenticación

    // Crear la solicitud SOAP
    let soapRequest = `
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:miserviciowsdl">
            <soapenv:Header/>
            <soapenv:Body>
                <urn:Servidor.ActualizarUsuario>
                    <idUser>${id}</idUser>
                    <nombre>${nombre}</nombre>
                    <usuario>${usuario}</usuario>
                    <contraseña>${password}</contraseña>
                    <token>${token}</token>
                </urn:Servidor.ActualizarUsuario>
            </soapenv:Body>
        </soapenv:Envelope>
    `;

    try {
        // Enviar la solicitud al servidor
        let response = await fetch('http://127.0.0.1/webservices/Proyectowebservices/invocar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'text/xml'
            },
            body: soapRequest
        });

        // Procesar la respuesta
        let textResponse = await response.text();

        // Parsear la respuesta
        let parser = new DOMParser();
        let xmlDoc = parser.parseFromString(textResponse, "text/xml");
        let result = xmlDoc.getElementsByTagName("return")[0]?.textContent || "Error desconocido";

        // Si la actualización fue exitosa
        if (result.includes("exitosamente")) {
            alert("Usuario actualizado correctamente");

            // Cerrar el modal
            window.location.href = "dashboard.php"; // Redirige a la dashboard
        }
    } catch (error) {
        // Aquí no se maneja el error
    }
});


</script>

    <!---       FIN        Modulo para ACTUALIZAR un nuevo usuario -------->



    <script>

async function obtenerUsuarios() {
    const url = "http://127.0.0.1/webservices/Proyectowebservices/invocar.php";
    const soapRequest = `
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
                          xmlns:urn="urn:miserviciowsdl">
            <soapenv:Header/>
            <soapenv:Body>
                <urn:Servidor.ObtenerUsuario>
                    <token>12345</token>
                </urn:Servidor.ObtenerUsuario>
            </soapenv:Body>
        </soapenv:Envelope>`;

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "text/xml; charset=utf-8",
                "SOAPAction": "urn:miserviciowsdl#ObtenerUsuario"
            },
            body: soapRequest
        });

        const textResponse = await response.text();
        console.log("Respuesta SOAP:", textResponse);

        // Procesar XML
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(textResponse, "text/xml");
        const usuariosXML = xmlDoc.getElementsByTagName("usuario");

        const tbody = document.getElementById("usuarios");
        tbody.innerHTML = ""; // Limpiar tabla

        if (usuariosXML.length > 0) {
            Array.from(usuariosXML).forEach(usuario => {
                const id = usuario.getElementsByTagName("idUser")[0].textContent;
                const nombre = usuario.getElementsByTagName("Name")[0].textContent;
                const username = usuario.getElementsByTagName("User")[0].textContent;

                // Crear fila de la tabla con datos y el botón de editar
                const row = `<tr>
                                <td>${id}</td>
                                <td>${nombre}</td>
                                <td>${username}</td>
                                <td> 

                                    <button class="btn btn-warning"  
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarUsuario">
                                        ✏ Editar
                                    </button>


                                    <button class="btn btn-danger btn-eliminar" data-id="${id}">🗑 Eliminar</button>    
                                </td>
                             </tr>`;
                tbody.innerHTML += row;
            });
        } else {
            tbody.innerHTML = "<tr><td colspan='3'>No se encontraron usuarios.</td></tr>";
        }
    } catch (error) {
        console.error("Error al obtener usuarios:", error);
        document.getElementById("usuarios").innerHTML = "<tr><td colspan='3'>Error al cargar los datos.</td></tr>";
    }
}

obtenerUsuarios();

    </script>



     <!---       INICIO        Modulo para eliminar un  usuario -------->

    <script>
        document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("usuarios").addEventListener("click", function (event) {
        if (event.target.classList.contains("btn-eliminar")) {
            let idUsuario = event.target.getAttribute("data-id");
            let token = "12345"; // Asegúrate de usar el token correcto

            if (!confirm("¿Estás seguro de que deseas eliminar este usuario?")) {
                return;
            }

            let soapRequest = `
                <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:miserviciowsdl">
                    <soapenv:Header/>
                    <soapenv:Body>
                        <urn:Servidor.EliminarUsuario>
                            <id>${idUsuario}</id>
                            <token>${token}</token>
                        </urn:Servidor.EliminarUsuario>
                    </soapenv:Body>
                </soapenv:Envelope>
            `;

            fetch("http://127.0.0.1/webservices/Proyectowebservices/invocar.php", {
                method: "POST",
                headers: {
                    "Content-Type": "text/xml"
                },
                body: soapRequest
            })
            .then(response => response.text())
            .then(data => {
                let parser = new DOMParser();
                let xmlDoc = parser.parseFromString(data, "text/xml");
                let result = xmlDoc.getElementsByTagName("return")[0].textContent;

                alert(result); // Muestra el mensaje del servidor

                if (result.includes("exitosamente")) {
                    obtenerUsuarios(); // Recargar la lista sin recargar la página
                }
            })
            .catch(error => console.error("Error:", error));
        }
    });
});

    </script>

    <!---       FIN        Modulo para eliminar un  usuario -------->


    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
