<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Wilfred BELLON
 * @author Pierre ARNOUX
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Origami_generator extends CI_Controller {

    private $override = array();
    private $association = array();
    private $entity_output = '';
    private $entity_path = APPPATH.'/models/Entity';

    const STARTCODE2KEEP = "//--START_PERSISTANT_CODE";
    const ENDCODE2KEEP = "//--END_PERSISTANT_CODE";

    /**
     * Contructeur
     */
    public function __construct() {
        parent::__construct();

        // Si le script n'est pas exécuté en PHP CLI
        if (!$this->input->is_cli_request()) {
            //exit("Le script doit être exécuté en CLI (Ex: #php index.php entitygenerator index)");
        }
        // Chargement de la config Origami
        $this->config->load('origami');
        $config_origami = $this->config->item('origami');

        // Assignation du répértoire de génération des entités
        $this->entity_path = (!empty($config_origami['entity_path'])) ? $config_origami['entity_path'] : $this->entity_path;

        // Paquet(s)
        $this->load->helper("text");
    }

    /**
     * Point d'entré
     */
    public function index() {
        $this->run();
    }

    /**
     * Configuration
     * @return array $db
     */
    private function _config() {
        // Fichier de configuration des bases de données
        $file_db_env = APPPATH.'config/'.ENVIRONMENT.'/database.php';
        $file_db = APPPATH.'config/database.php';
        $file_path = (file_exists($file_db_env)) ? $file_db_env : $file_db;

        // Inclusion de la configuration
        require($file_path);

        // Retourne la configuration
        return $db;
    }

    /**
     * Création d'un répertoire
     * @param string $dir
     * @return boolean
     */
    private function _dir($dir) {
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0755, TRUE)) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Sauvegarde les surcharges des modèles
     */
    private function _save_override($dir) {

        echo '<pre>';
        echo '<h1>CLEAN</h1>';

        foreach (glob($dir) as $file_path) {
            if (is_file($file_path)) {
                $file = basename($file_path);
                $path = dirname($file_path);
                $file_content = file_get_contents($file_path);
                $override = '';
                $particules = explode(self::STARTCODE2KEEP, $file_content);

                if (!empty($particules[1])) {
                    $particules = explode(self::ENDCODE2KEEP, $particules[1]);
                    $override = $particules[0];
                }

                if (!empty($override)) {
                    $this->override[$file] = "\r\n\t".self::STARTCODE2KEEP.($override).self::ENDCODE2KEEP."\r\n";
                }

                echo "Suppression du fichier <b>$file</b> du répertoire $path : ";

                if (unlink($file_path)) {
                    echo '<b style="color:green">OK</b><br />';
                } else {
                    echo '<b style="color:red">KO</b><br />';
                }
            }
        }
    }

    /**
     * Génère les associations
     */
    private function _association_many($namespace) {
        $relation_inverse = array(
            "belongs_to" => "has_many",
            "has_many" => "belongs_to",
            "has_one" => "has_one"
        );

        if (!$this->{"db_$namespace"}->initialize())
            exit("Impossible de se connecté à la base <b>$namespace</b> (database.php).");

        $query_table = $this->{"db_$namespace"}->query("SHOW TABLE STATUS");

        foreach ($query_table->result_array() as $table) {
            // création des relations (clés étrangères d'inno db préalablement définies en base)
            $sql = 'SELECT DISTINCT(CONSTRAINT_NAME),TABLE_NAME,COLUMN_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE CONSTRAINT_NAME != "PRIMARY"
                    AND  CONSTRAINT_SCHEMA = "'.$namespace.'"
                    AND  TABLE_NAME = "'.$table['Name'].'"';

            $query_relations = $this->{"db_$namespace"}->query($sql);

            if ($query_relations->num_rows() > 0) {
                foreach ($query_relations->result_array() as $data) {
                    $referenced_table_name = $data["REFERENCED_TABLE_NAME"];

                    // le plus courant
                    $relation_type = "has_one";

                    $query_field = $this->{"db_$namespace"}->query('SHOW FULL COLUMNS FROM `'.$table['Name'].'`');
                    $relations_comments = array();

                    if ($query_field->num_rows() > 0) {
                        foreach ($query_field->result_array() as $field) {
                            $relations_comments[$field['Field']] = $field['Comment'];
                        }
                    }

                    if (!empty($relations_comments[$data["COLUMN_NAME"]])) {
                        $relation_type = $relations_comments[$data["COLUMN_NAME"]];

                        if ($relation_type != "has_one" && $relation_type != "has_many" && $relation_type != "belongs_to") {
                            $relation_type = "";
                        }
                    }

                    if ($relation_type != "" && strtolower($relation_type) != "has_one" && empty($already_seen[$referenced_table_name][$table['Name']])) {
                        $already_seen[$referenced_table_name][$table['Name']] = TRUE;

                        // STOCKAGE DES RELATION INVERSES
                        $referenced_table_name_t = strtolower($table['Name']);
                        $this->association[$referenced_table_name]['php'][] = "\t\tarray('association_key' => '$referenced_table_name_t', 'entity' => '\\Entity\\$namespace\\{$table['Name']}', 'type' => '{$relation_inverse[$relation_type]}', 'primary_key' => 'id', 'foreign_key' => '{$data["COLUMN_NAME"]}'),\r\n";
                        $this->association[$referenced_table_name]['javadoc'][] = "\t * @method \\Entity\\$namespace\\$referenced_table_name_t $referenced_table_name_t() {$relation_inverse[$relation_type]}\r\n";
                    }
                }
            }
        }

        echo '<hr />';
    }

    /**
     * Création des modèles d'une base de donée
     */
    private function _create_entity($namespace) {
        $relations_comments = array();

        echo '<h1>GENERATION</h1>';

        $query_table = $this->{"db_$namespace"}->query("SHOW TABLE STATUS");

        foreach ($query_table->result_array() as $table) {
            $this->entity_output = '';

            $this->_append("<?php\r\n");
            $this->_append("namespace Entity\\$namespace;\r\n");
            $this->_append("\r\n");
            $this->_append("defined('BASEPATH') OR exit('No direct script access allowed');\r\n");
            $this->_append("\r\n");

            $class_name = $table['Name'];

            $this->_append("class $class_name extends \Origami\Entity\r\n");
            $this->_append("{\r\n");

            // GESTION DES CONSTANTES
            // on regarde si dans la table il y a la colonne constant
            $constants = array();

            $query_columns = $this->{"db_$namespace"}->query("SHOW COLUMNS FROM `{$table['Name']}`");

            foreach ($query_columns->result_array() as $column) {
                if ($column['Field'] === "constant") {

                    $query_constant = $this->{"db_$namespace"}->query("SELECT `id`, `constant` FROM `{$table['Name']}` WHERE `constant` IS NOT NULL;");

                    foreach ($query_constant->result_array() as $val) {

                        foreach ($val as $k => $v) {
                            if ($k === 'constant' && !empty($v)) {

                                $constant = $this->_strtoconstante($v);

                                if (!in_array($constant, $constants)) {
                                    $constants[] = $constant;
                                } else {
                                    die('<b style="color:red">ATTENTION : Vous avez deux fois la même constante '.$constant.' dans la table '.$table['Name'].'</b><br />');
                                }

                                $this->_append("\tconst $constant = {$val['id']};\r\n");
                            }
                        }
                    }
                }
            }

            $this->_append("\r\n");
            $this->_append("\tpublic static \$table = '{$table['Name']}';\r\n");
            $this->_append("\r\n");

            $query_field = $this->{"db_$namespace"}->query('SHOW FULL COLUMNS FROM `'.$table['Name'].'`');

            if ($query_field->num_rows() > 0) {

                $primary_keys = "";

                $foreign_keys = "\tpublic static \$foreign_key = array(\r\n";
                $flag_foreign_keys = false;

                $fields_javadoc_buffer = "\t/**\r\n";
                $fields_buffer = "\tpublic static \$fields = array(\r\n";

                $type = array();

                foreach ($query_field->result_array() as $field) {
                    if (strpos($field['Type'], "(") !== FALSE) {
                        $type = explode('(', $field['Type']);
                    } else {
                        $type[0] = $field['Type'];
                    }

                    $type_date = NULL;

                    switch ($type[0]) {
                        case 'bigint':
                        case 'mediumint':
                        case 'tinyint':
                        case 'smallint':
                        case 'int':
                            $type[0] = 'int';
                            break;
                        case 'float':
                        case 'double':
                            $type[0] = 'float';
                            break;
                        case 'date':
                            $type_date = 'Y-m-d';
                            $type[0] = 'date';
                            break;
                        case 'datetime':
                        case 'timestamp':
                            $type_date = 'Y-m-d H:i:s';
                            $type[0] = 'date';
                            break;
                        default:
                            $type[0] = 'string';
                            break;
                    }
                    // on stocke les commentaires (notamment pour les clés étrangères
                    $relations_comments[$field['Field']] = $field['Comment'];

                    //Gestion des description des champs dans le commentaire de celui ci
                    //Exemple encrypt ou has_one pour les relations

                    $allow_null = false;
                    $encrypt = false;
                    $binary = false;

                    if ($field['Null'] == 'YES')
                        $allow_null = true;

                    $tabField = explode('|', $field['Comment']);
                    foreach ($tabField as $fieldAjout) {
                        switch ($fieldAjout) {
                            case 'encrypt':
                                $encrypt = true;
                                break;
                            case 'binary':
                                $binary = true;
                                break;
                        }
                    }

                    $fields_buffer .= "\t\tarray(";

                    $fields_javadoc_type = ($type[0] === "int") ? "integer" : $type[0];
                    $fields_javadoc_buffer .= "\t * @property $fields_javadoc_type \${$field['Field']}\r\n";

                    $fields_buffer .= "'name' => '{$field['Field']}', 'type' => '{$type[0]}'";

                    if (!empty($type_date))
                        $fields_buffer .= ", 'date_format' => '$type_date'";

                    if ($allow_null)
                        $fields_buffer .= ", 'allow_null' => true";

                    if ($encrypt)
                        $fields_buffer .= ", 'encrypt' => true";

                    if ($binary)
                        $fields_buffer .= ", 'binary' => true";

                    if ($field['Key'] == 'PRI')
                        $primary_keys .= "\r\n\t".'public static $primary_key = \''.$field['Field'].'\';'."\r\n";

                    if ($field['Key'] == 'MUL') {
                        $foreign_keys .= "\t\t".'"'.$field['Field'].'"'.",\r\n";
                        $flag_foreign_keys = true;
                    }

                    $fields_buffer .= "),\r\n";
                }

                $fields_javadoc_buffer .= "\t */\r\n";

                $this->_append($fields_javadoc_buffer);

                $fields_buffer .= "\t);\r\n";
                $fields_buffer = str_replace(",\r\n\t);", "\r\n\t);", $fields_buffer);

                $this->_append($fields_buffer);

                if ($flag_foreign_keys) {
                    $foreign_keys.="end";
                    $foreign_keys = str_replace(",\r\nend", "", $foreign_keys);
                }
                $foreign_keys.="\r\n\t);\r\n";
                $this->_append($primary_keys);
            }

            $this->_append("\r\n");

            /* ---------------------------------------- */
            /*    ECRITURE DES RELATIONS                */
            /* ---------------------------------------- */
            // création des relations (clés étrangères d'inno db préalablement définies en base)
            $sql_foreign_keys = 'SELECT DISTINCT(CONSTRAINT_NAME), TABLE_NAME, COLUMN_NAME,REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                                    FROM information_schema.KEY_COLUMN_USAGE
                                    WHERE CONSTRAINT_NAME != "PRIMARY"
                                        AND  CONSTRAINT_SCHEMA = "'.$namespace.'"
                                        AND  TABLE_NAME = "'.$table['Name'].'"';
            $query_relations = $this->{"db_$namespace"}->query($sql_foreign_keys);
            $asso = false;

            if ($query_relations->num_rows() > 0 || (isset($this->association[$table['Name']]) && count($this->association[$table['Name']])) > 0) {
                $asso = true;

                $relations_javadoc_buffer = "\t/**\r\n";
                $relations_buffer = "\tpublic static \$associations = array(\r\n";
                // écriture relation inverses
                if (isset($this->association[$table['Name']])) {
                    foreach ($this->association[$table['Name']]['php'] as $rel) {
                        $relations_buffer .= $rel;
                    }

                    foreach ($this->association[$table['Name']]['javadoc'] as $rel_j) {
                        $relations_javadoc_buffer .= $rel_j;
                    }
                }

                foreach ($query_relations->result_array() as $data) {

                    $relation_name = str_replace("_id", "", $data["COLUMN_NAME"]);
                    $referenced_table_name = $data["REFERENCED_TABLE_NAME"];
                    $referenced_column_name = $data["REFERENCED_COLUMN_NAME"];
                    $column_name = $data["COLUMN_NAME"];
                    $relation_type = 'has_one'; // le plus courant

                    if (!empty($relations_comments[$data["COLUMN_NAME"]])) {
                        $relation_type = $relations_comments[$data["COLUMN_NAME"]];

                        // sécurité
                        if ($relation_type != 'has_one' && $relation_type != 'has_many' && $relation_type != 'belongs_to') {
                            $relation_type = '';
                        }
                    }

                    if (!empty($relation_type) && !empty($referenced_table_name)) {
                        $relations_javadoc_buffer .= "\t * @method \\Entity\\$namespace\\$referenced_table_name $referenced_table_name() $relation_type\r\n";
                        $relations_buffer .= "\t\tarray('association_key' => '$referenced_table_name', 'entity' => '\\Entity\\$namespace\\{$referenced_table_name}', 'type' => '$relation_type', 'primary_key' => '$referenced_column_name', 'foreign_key' => '$column_name'),\r\n";
                    }
                }

                $relations_buffer .= "\t);\r\n";
                $relations_buffer = str_replace(",\r\n\t);", "\r\n\t);", $relations_buffer);

                $relations_javadoc_buffer .= "\t */\r\n";

                $this->_append($relations_javadoc_buffer);
                $this->_append($relations_buffer);
            }

            $file_name = $table['Name'].'.php';

            // Si il exite un override
            if (!empty($this->override[$file_name])) {
                $this->_append($this->override[$file_name]);
            }

            $this->_append('}'."\r\n");
            $this->_append("\r\n");

            $file_path = $this->entity_path.'/'.$namespace.'/'.$file_name;

            if (touch($file_path)) {
                file_put_contents($file_path, $this->entity_output);
                chmod($file_path, 0644);

                echo 'Creation du fichier : <b>'.$file_name.'</b> : <b style="color:green">OK</b><br />';
            } else {
                echo 'Creation du fichier : <b>'.$file_name.'</b> : <b style="color:red">KO</b><br />';
            }
        }

        $this->association = array();
        $this->override = array();

        echo '<hr />';
        echo '<h2>DONE ;)</h2>';
    }

    /**
     * Exécute le script
     */
    public function run() {
        $config = $this->_config();

        foreach ($config as $namespace => $db) {
            echo "<h1>Base de donnée <strong>$namespace</strong></h1><br />";

            // Création du répertoire
            $this->_dir($this->entity_path.'/'.$namespace);

            // Récupère les données des anciens modèles
            $this->_save_override($this->entity_path.'/'.$namespace.'/*');

            // Stock la nouvelle connexion à la base de donnée
            $this->{"db_$namespace"} = $this->load->database($namespace, TRUE);

            // Stock les association many
            $this->_association_many($namespace);

            // Création des modèles
            $this->_create_entity($namespace);

            // Profiler
            $this->output->enable_profiler(TRUE);
        }
    }

    /**
     * Contenu d'un modèle
     * @param string $output
     */
    private function _append($output) {
        $this->entity_output .= $output;
    }

    /**
     * Génère le nom d'une constante
     * @param string $chaine
     * @return string
     */
    private function _strtoconstante($chaine) {
        $chaine = trim($chaine);
        $chaine = convert_accented_characters($chaine);
        $chaine = preg_replace('#([^.a-z0-9]+)#i', '_', $chaine);
        $chaine = preg_replace('#-#', '_', $chaine);
        $chaine = preg_replace('#_{2,}#', '_', $chaine);
        $chaine = preg_replace('#_$#', '', $chaine);
        $chaine = preg_replace('#^_#', '', $chaine);
        $chaine = preg_replace('#\.#', '', $chaine);

        return strtoupper($chaine);
    }

}

/* End of file entitygenerator.php */
/* Location: ./application/controllers/entitygenerator.php */
