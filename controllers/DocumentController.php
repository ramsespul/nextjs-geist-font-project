<?php
class DocumentController {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }

    public function getProcedimientos($departamento_id = null) {
        try {
            $sql = "SELECT p.*, d.nombre as departamento_nombre 
                    FROM procedimientos p 
                    LEFT JOIN departamentos d ON p.departamento_id = d.id";
            
            if ($departamento_id) {
                $sql .= " WHERE p.departamento_id = :departamento_id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':departamento_id', $departamento_id);
            } else {
                $stmt = $this->db->prepare($sql);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get Procedimientos Error: " . $e->getMessage());
            return [];
        }
    }

    public function buscarProcedimientos($query) {
        try {
            $sql = "SELECT p.*, d.nombre as departamento_nombre 
                    FROM procedimientos p 
                    LEFT JOIN departamentos d ON p.departamento_id = d.id 
                    WHERE p.titulo LIKE :query 
                    OR p.descripcion LIKE :query";
            
            $searchQuery = "%{$query}%";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':query', $searchQuery);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Search Procedimientos Error: " . $e->getMessage());
            return [];
        }
    }

    public function registrarVisualizacion($usuario_id, $procedimiento_id, $accion = 'visualizado') {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO historial_visualizacion 
                (usuario_id, procedimiento_id, accion) 
                VALUES (:usuario_id, :procedimiento_id, :accion)"
            );
            
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':procedimiento_id', $procedimiento_id);
            $stmt->bindParam(':accion', $accion);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Register View Error: " . $e->getMessage());
            return false;
        }
    }

    public function subirProcedimiento($titulo, $descripcion, $archivo, $departamento_id) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO procedimientos 
                (titulo, descripcion, archivo, departamento_id) 
                VALUES (:titulo, :descripcion, :archivo, :departamento_id)"
            );
            
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':archivo', $archivo);
            $stmt->bindParam(':departamento_id', $departamento_id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Upload Procedimiento Error: " . $e->getMessage());
            return false;
        }
    }

    public function getDepartamentos() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM departamentos");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get Departamentos Error: " . $e->getMessage());
            return [];
        }
    }

    public function getProcedimiento($id) {
        try {
            $stmt = $this->db->prepare(
                "SELECT p.*, d.nombre as departamento_nombre 
                FROM procedimientos p 
                LEFT JOIN departamentos d ON p.departamento_id = d.id 
                WHERE p.id = :id"
            );
            
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get Procedimiento Error: " . $e->getMessage());
            return null;
        }
    }
}
?>
