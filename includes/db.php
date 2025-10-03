<?php
require_once __DIR__ . '/config.php';

class Database
{
    private $supabase_url;
    private $supabase_key;

    public function __construct()
    {
        // ✅ REMOVIDO O ESPAÇO NO FINAL DA URL!
        $this->supabase_url = 'https://sfjbijonfjdnzdjrmsfu.supabase.co';
        $this->supabase_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InNmamJpam9uZmpkbnpkanJtc2Z1Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTk1MTk4NjQsImV4cCI6MjA3NTA5NTg2NH0.q5T1otmwG0PX1e19xyCZnMxKK4ixVE3VmTIC6-_cC2I';
    }

    private function request($method, $endpoint, $data = null)
    {
        $url = $this->supabase_url . '/rest/v1/' . $endpoint;

        $headers = [
            'apikey: ' . $this->supabase_key,
            'Authorization: Bearer ' . $this->supabase_key,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Evita travamentos

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // ✅ SEMPRE retornar um array para métodos de leitura (GET)
        // Para falhas, logamos (opcional) e retornamos []
        if ($httpCode >= 200 && $httpCode < 300) {
            $decoded = json_decode($response, true);
            // Se for um array, retorna; senão, retorna vazio
            return is_array($decoded) ? $decoded : [];
        }

        // ✅ Em caso de erro, NÃO retorna false — retorna array vazio
        // Isso evita o erro do count()
        error_log("Supabase error [$method $endpoint]: HTTP $httpCode | cURL: $error | Response: " . substr($response, 0, 200));
        return [];
    }

    public function select($table, $columns = '*', $conditions = [])
    {
        // Monta a query com filtros
        $selectPart = urlencode($columns);
        $endpoint = $table . '?select=' . $selectPart;

        foreach ($conditions as $key => $value) {
            // Suporte a operadores como 'gte.data'
            if (strpos($value, '.') !== false) {
                // Ex: 'gte.2025-04-05'
                $endpoint .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $endpoint .= '&' . urlencode($key) . '=eq.' . urlencode($value);
            }
        }

        return $this->request('GET', $endpoint);
    }

    public function selectOne($table, $conditions = [])
    {
        $result = $this->select($table, '*', $conditions);
        return !empty($result) ? $result[0] : null;
    }

    public function insert($table, $data)
    {
        return $this->request('POST', $table, $data);
    }

    public function update($table, $id, $data)
    {
        $endpoint = $table . '?id=eq.' . urlencode($id);
        return $this->request('PATCH', $endpoint, $data);
    }

    public function delete($table, $id)
    {
        $endpoint = $table . '?id=eq.' . urlencode($id);
        return $this->request('DELETE', $endpoint);
    }

    public function query($sql)
    {
        $endpoint = 'rpc/query';
        return $this->request('POST', $endpoint, ['query' => $sql]);
    }
}

$db = new Database();
