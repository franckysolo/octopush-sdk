<?php
/**
 * The Message class
 *
 * @version 1.0.1
 * @package Octopush
 * @author franckysolo <franckysolo@gmail.com>
 */
namespace Octopush;

/**
 * A representation of Octopush Message
 *
 * @see http://www.octopush.com/api-sms-documentation
 */
class Message
{
    /**
     * Sms low cost sms_type param
     *
     * @var string
     */
    const SMS_LOW_COST = 'XXX';

    /**
     * Sms premium sms_type param
     *
     * @var string
     */
    const SMS_PREMIUM = 'FR';

    /**
     * Sms world sms_type param
     *
     * @var string
     */
    const SMS_WORLD = 'WWW';

    /**
     * Sms sending real mode value param
     *
     * @var string
     */
    const REAL_MODE = 'real';

    /**
     * Sms sending simulation mode value param
     *
     * @var string
     */
    const SIMULATION_MODE = 'simu';

    /**
     * Sms sending immedialty mode value param
     *
     * @var int
     */
    const NO_DELAY = 1;

    /**
     * Sms sending with delay mode value param
     *
     * @var int
     */
    const WITH_DELAY = 2;

    /**
     * Characters count as double
     *
     * @var array
     */
    protected static $chars = [
      '{', '}', '€', '[', ']', '~', '^',
      '¨', '|', '&', '$', '@', '\n',
      '\r', '\t'
    ];

    /**
     * Required Parameters for api request with api_key & user_login
     *
     * @var array
     */
    protected $requiredKeys = [
      'sms_recipients',
      'sms_text',
      'sms_type',
      'sms_sender'
    ];

    /**
     * Optionnals Parameters for api request
     *
     * @var array
     */
    protected $optionalKeys = [
      'sms_mode',
      'sending_date',
      'sending_time',
      'sending_period',
      'recipients_first_names',
      'recipients_last_names',
      'sms_fields_1',
      'sms_fields_2',
      'sms_fields_3',
      'request_mode',
      'request_id',
      'with_replies',
      'transactional',
      'msisdn_sender',
      'request_keys',
      'request_sha1'
    ];

    /**
     * Data to encrypt in the url if request_keys is sets
     *
     * @var array
     */
    protected $encryptData = [
      'T' => 'sms_text',
      'R' => 'sms_recipients',
      'Y' => 'sms_type',
      'S' => 'sms_sender',
      'D' => 'sms_date',
      'a' => 'recipients_first_names',
      'b' => 'recipients_last_names',
      'c' => 'sms_fields_1',
      'd' => 'sms_fields_2',
      'e' => 'sms_fields_3',
      'W' => 'with_replies',
      'N' => 'transactional',
      'Q' => 'request_id'
    ];

    /**
     * Create a new octopush sms message
     *
     * @param array $params the array params
     * @see $requiredKeys
     * @see $optionalKeys
     */
    public function __construct($params = [])
    {
        $this->setParams($params);
    }

    /**
     * Set the parameters message
     *
     * @param array $params The parameters message required and optionnals
     * @return \Octopush\Message
     * @throws \InvalidArgumentException
     */
    public function setParams(array $params = [])
    {
        if (empty($params) || count($params) < 4) {
            throw new \InvalidArgumentException(
            'Missing required params for Octopush message'
          );
        }

        foreach ($params as $key => $param) {
            if (!in_array($key, array_merge($this->requiredKeys, $this->optionalKeys))) {
                throw new \InvalidArgumentException(
                  'Missing required params for Octopush message'
                );
            }
        }

        foreach ($params as $key => $value) {
            $method = 'set' . $this->setMethod($key);
            $this->$method($value);
        }

        // set default params
        $this->params['sending_time'] = (new \DateTime())->getTimestamp();

        if (isset($this->params['sending_date'])) {
            $this->params['sms_mode'] = static::WITH_DELAY;
        }

        if (isset($this->params['request_keys'])) {
            $this->params['request_sha1'] = $this->encrypt($this->params);
        }

        if ($this->params['sms_type'] === 'FR') {
            $this->params['sms_text'] .= PHP_EOL . 'STOP au XXXXX';
        }

        return $this;
    }

    /**
     * Returns the parameters message
     *
     * @return array The parameters message required and optionnal
     * in php array format
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Encoding request_sha1 	Optionnel
     *
     * Cette valeur contient le sha1 de la concaténation des valeurs des champs
     * choisis dans la variable request_keys et concaténés dans le même
     * ordre que les clés.
     *
     * @param  array $params the params
     * @return string the encrypt string code
     */
    public function encrypt(array $params = [])
    {
        $requestString = '';
        $requestKey = $this->params['request_keys'];
        for ($i = 0, $n = strlen($requestKey); $i < $n; ++$i) {
            $char = $requestKey[$i];
            if (!isset($this->encryptData[$char])
              || !isset($params[$this->encryptData[$char]])) {
                continue;
            }
            $requestString .= $params[$this->encryptData[$char]];
        }

        return sha1($requestString);
    }

    /**
     * Set the method name from array key
     *
     * @param string $key The array key
     * @return string The fomatted method name
     */
    public function setMethod($key)
    {
        $array = array_map('ucfirst', explode('_', $key));
        return str_replace('_', '', implode('_', $array));
    }

    /**
     * Liste des numéros au format international +XXZZZZZ,
     *
     * @param mixed $recipients  numéros de phone
     * @return \Octopush\Message
     */
    public function setSmsRecipients($recipients)
    {
        $this->params['sms_recipients'] =
          is_array($recipients) ? implode(',', $recipients) : $recipients;
        return $this;
    }

    /**
     * Texte du message
     *
     * @param string $text (maximum 459 caractères)
     * @return \Octopush\Message
     */
    public function setSmsText($text)
    {
        if (strlen($text) > 459) {
            $message = sprintf("The sms text is too long");
            throw new \InvalidArgumentException($message, 500);
        }

        $this->params['sms_text'] = trim($text);
        return $this;
    }
    /**
     * Type de SMS : XXX = SMS LowCost ; FR = SMS Premium; WWW = SMS Monde.
     *
     * En France, si la mention « STOP au XXXXX » est absente de votre texte,
     * l'API renverra une erreur.
     *
     * @param string $type
     * @return \Octopush\Message
     */
    public function setSmsType($type)
    {
        if (!in_array($type, ['XXX', 'WWW', 'FR'])) {
            $message = sprintf('The sms type %s is invalid', $type);
            throw new \InvalidArgumentException($message, 500);
        }
        $this->params['sms_type'] = $type;
        return $this;
    }

    /**
     * Expéditeur du message (si l'opérateur le permet),
     * 3 à 11 caractères alpha- numériques (a-zA-Z).
     *
     * @param string $sender
     * @return \Octopush\Message
     */
    public function setSmsSender($sender)
    {
        $length = strlen($sender);
        $validator = preg_match('/[\w]+/', $sender);
        if ($length < 3 || $length > 13 || !$validator) {
            $message = sprintf("The sms sender %s is invalid", $sender);
            throw new \InvalidArgumentException($message, 500);
        }
        $this->params['sms_sender'] = $sender;
        return $this;
    }

    /**
     *  request_mode 	Optionnel
     *
     *  Permet de choisir le mode simulation
     *  avec la valeur 'simu'. * défaut : real
     *
     * @param string $mode
     * @return \Octopush\Message
     */
    public function setRequestMode($mode)
    {
        if (!in_array($mode, ['real', 'simu'])) {
            $message = sprintf(
              'The request mode %s is not supported, real or simu expected!',
              $mode
            );
            throw new \InvalidArgumentException($message, 500);
        }
        $this->params['request_mode'] = $mode;
        return $this;
    }

    /**
     *  sms_mode 	Optionnel
     *
     *  Mode d'envoi :
     *    défaut : 1
     *      * 1 = Envoi Instantané,
     *      * 2 = Envoi Différé (il faut alors spécifier la date)
     *
     *  @param int $mode
     *  @return \Octopush\Message
     */
    public function setSmsMode($mode)
    {
        if (!in_array($mode, [1, 2])) {
            $message = sprintf(
              'The sms mode %s is not supported, 1 or 2 expected!',
              $mode
            );
            throw new \InvalidArgumentException($message, 500);
        }
        $this->params['sms_mode'] = (int) $mode;
        return $this;
    }

    /**
     * sending_date 	Conditionnel
     *
     *  [Si sms_mode = 2, deprecated]
     *  Timestamp, indiqué en GMT+1
     *
     * @param \DateTime $timestamp
     * @return \Octopush\Message
     */
    public function setSendingDate(\DateTime $timestamp)
    {
        $this->params['sending_date'] = $timestamp->getTimestamp();
        return $this;
    }
    /**
     * sending_time 	Optionnel
     *
     * Timestamp, indiqué en GMT+1
     *
     * @param \DateTime $timestamp
     * @return \Octopush\Message
     */
    public function setSendingTime(\DateTime $timestamp)
    {
        $this->params['sending_time'] = $timestamp->getTimestamp();
        return $this;
    }

    /**
     *   sending_period 	Optionnel
     *   Delai souhaité avant envoi.
     *
     *   Ce champ vous permet de définir le temps qui
     *   vous sépare de la date souhaitée
     *   et d'éviter les confusions de timezone
     *
     * @param \DateTime $timestamp
     * @return \Octopush\Message
     */
    public function setSendingPeriod(\DateTime $timestamp)
    {
        $this->params['sending_period'] = $timestamp->getTimestamp();
        return $this;
    }

    /**
     *  with_replies 	Optionnel
     *   Instancier à 1
     *   pour indiquer que vous souhaitez les réponses aux SMS envoyés
     *
     * @param int $withReply
     * @return \Octopush\Message
     */
    public function setWithReplies($withReply = true)
    {
        if ($withReply) {
            $this->params['with_replies'] = 1;
        }
        return $this;
    }
    /**
     * transactional 	Optionnel
     *
     * Instancier à 1 pour les envois de type confirmation,
     * ou alerte (7j/7, h24) INTERDICTION FORMELLE d'envoyer du SMS marketing
     * avec cette option (tout abus est passible d'une suspension
     * immédiate du compte, ainsi que d'une amende de 1000€
     * par infraction constatée)
     *
     * @see http://www.octopush.com/api-sms-doc/sms-transactionnel
     * @param bool $transactional
     * @return \Octopush\Message
     */
    public function setTransactional($transactional = true)
    {
        if ($transactional) {
            $this->params['transactional'] = 1;
        }
        return $this;
    }

    /**
     *   request_keys 	Optionnel
     *
     *   Contient la liste des clés des champs de la requête que vous souhaitez
     *   ajouter dans le hash sha1.
     *   Exemple : 'TRYS' (pour les champs sms_text, sms_recipients, sms_type, sms_sender).
     *   Voir le tableau des clés en annexe.
     *
     * @see http://www.octopush.com/api-sms-doc/parametres
     * @param string $requestKeys
     * @return \Octopush\Message
     */
    public function setRequestKeys($requestKeys)
    {
        $this->params['request_keys'] = $requestKeys;
        return $this;
    }

    /**
     *  request_id 	Optionnel
     *
     *  Permet d'ajouter une sécurité à l'envoi.
     *
     *  Si ce champ est différent de null, alors le système viendra vérifier
     *  s'il n'y pas déjà un de vos envois ayant
     *  le même request_id. Si c'est le cas,
     *  la requête est ignorée.
     *
     * @param string $rid
     * @return \Octopush\Message
     */
    public function setRequestId($rid)
    {
        $this->params['request_id'] = $rid;
        return $this;
    }

    /**
     * msisdn_sender 	Optionnel
     *
     * défaut : 0.
     * Certains opérateurs internationaux autorisent
     * les numéros de téléphone comme émetteur.
     * Dans ce cas, ce champ doit être à 1.
     *
     * @param int $sender
     * @return \Octopush\Message
     */
    public function setMsisdnSender($sender)
    {
        if ($sender > 1) {
            $message = sprintf(
              'This Msisdn %s is not supported 0 or 1 expected!',
              $sender
            );
            throw new \InvalidArgumentException($message, 500);
        }
        $this->params['msisdn_sender'] = $sender;
        return $this;
    }

    /**
     * recipients_first_names 	Optionnel
     *
     * Remplacent la chaîne {prenom} de votre message.
     *
     * @param array $firstnames
     * @return \Octopush\Message
     */
    public function setRecipientsFirstNames(array $firstnames = [])
    {
        $this->params['recipients_first_names'] = implode(',', $firstnames);
        return $this;
    }
    /**
     * recipients_last_names 	Optionnel
     *
     * Remplacent la chaîne {nom} de votre message,
     * séparés par des virgules.
     *
     * @param array $lastnames
     * @return \Octopush\Message
     */
    public function setRecipientsLastNames(array $lastnames = [])
    {
        $this->params['recipients_last_names'] = implode(',', $lastnames);
        return $this;
    }
    /**
     * sms_fields_1 	Optionnel
     *
     * Remplacent la chaîne {ch1} de votre message, séparés par des virgules.
     *
     * @param array $fields
     * @return \Octopush\Message
     */
    public function setSmsFields1(array $fields = [])
    {
        $this->params['sms_fields_1'] = implode(',', $fields);
        return $this;
    }
    /**
     * sms_fields_2 	Optionnel
     *
     * Remplacent la chaîne {ch2} de votre message, séparés par des virgules.
     *
     * @param array $fields
     * @return \Octopush\Message
     */
    public function setSmsFields2(array $fields = [])
    {
        $this->params['sms_fields_2'] = implode(',', $fields);
        return $this;
    }
    /**
     * sms_fields_3 	Optionnel
     *
     * Remplacent la chaîne {ch3} de votre message, séparés par des virgules.
     *
     * @param array $fields
     * @return \Octopush\Message
     */
    public function setSmsFields3(array $fields = [])
    {
        $this->params['sms_fields_3'] = implode(',', $fields);
        return $this;
    }
}
