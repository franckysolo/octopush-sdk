<?php
/**
 * The Message class
 *
 * @version 1.0.2
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
     * The sms_recipients field - [Required]
     *
     * List of numbers in international format + XXZZZZZ separated by commas.
     *
     * Liste des numéros au format international +XXZZZZZ séparés par des ,
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
     * The sms_text field - [Required]
     *
     * Message text (maximum 459 characters).
     *
     * Texte du message (maximum 459 caractères)
     *
     * @param string $text The string message text
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
     * The sms_type field - [Required]
     *
     * SMS Type: XXX = Low Cost SMS; FR = Premium SMS; WWW = Global SMS.
     * In France, if "STOP XXXXX" is missing from your text, the API will
     * return an error.
     *
     * Type de SMS : XXX = SMS LowCost ; FR = SMS Premium; WWW = SMS Monde.
     * En France, si la mention « STOP au XXXXX » est absente de votre texte,
     * l'API renverra une erreur.
     *
     * @param string $type The message type maybe XXX | FR | WWW
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
     * Sender of the message (if the user allows it), 3-11 alphanumeric characters (a-zA-Z).
     *
     * Expéditeur du message (si l'opérateur le permet), 3 à 11 caractères alpha-numériques (a-zA-Z).
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
     *  The request_mode param 	Optionnel
     *
     *  Allows you to choose simulation mode with the value 'simu'. * Default: real
     *
     *  Permet de choisir le mode simulation avec la valeur 'simu'. * défaut : real
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
     *  The sms_mode param - [Optionnel]
     *
     *  Sending profile :
     *    default : 1
     *    * 1 = Instant sending,
     *    * 2 = Delayed sending (you must specify the date)
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
     * The sending_date param - [Conditional]
     *
     *  [If mode = 2, deprecated] Timestamp, shown in GMT + 1
     *
     *  [Si sms_mode = 2, deprecated] Timestamp, indiqué en GMT+1
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
     * The sending_time param - [Optional]
     *
     * Timestamp, shown in GMT + 1
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
     *   The sending_period param -	[Optional]
     *
     *   Period desired (in seconds) before the sending.
     *   This field allows you to define the time before
     *   the desired date and to prevent timezone confusing
     *
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
     *   The with_replies param	- [Optionnel]
     *
     *   Set to 1 to indicate that you want answers to messages that were sent
     *
     *   Instancier à 1 pour indiquer que vous souhaitez les réponses aux SMS envoyés
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
     * The transactional param - [Optional]
     *
     *  Set to 1 to confirm these messages, or alert us(24/7) to a FORMAL BAN on
     *  SMS marketing with this option (any abuse of this feature may lead to
     *  immediate account suspension and a fine of €1,000 per violation)
     *
     *  Instancier à 1 pour les envois de type confirmation,
     *  ou alerte (7j/7, h24) INTERDICTION FORMELLE d'envoyer du SMS marketing
     *  avec cette option (tout abus est passible d'une suspension
     *  immédiate du compte, ainsi que d'une amende de 1000€
     *  par infraction constatée)
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
     *   The request_keys param -	[Optional]
     *
     *   Lists the key fields of the application you want to add in the sha1 hash.
     *   Example: 'TRYS ' (for fields sms_text, sms_recipients, sms_type,
     *   sms_sender). See the table of keys attached.
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
     *  The request_id param - [Optional]
     *
     *  Specifies secure sending. If the field is not null, then the system
     *  will check if there are already messages with the same request_id.
     *  If there are, the request is ignored.
     *
     *  Permet d'ajouter une sécurité à l'envoi.
     *  Si ce champ est différent de null, alors le système viendra vérifier
     *  s'il n'y pas déjà un de vos envois ayant le même request_id.
     *  Si c'est le cas, la requête est ignorée.
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
     * The msisdn_sender param - [Optional]
     *
     * Default: 0. Some operators allow international phone numbers as sender.
     * In this case, the field must be 1.
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
     * The recipients_first_names param -	[Optional]
     *
     * Replacing the string {prenom} of your message.
     *
     * Remplacent la chaîne {prenom} de votre message.
     *
     * @param array $firstnames The array of firstnames
     * @return \Octopush\Message
     */
    public function setRecipientsFirstNames(array $firstnames = [])
    {
        $this->params['recipients_first_names'] = implode(',', $firstnames);
        return $this;
    }
    /**
     * The recipients_last_names 	- [Optional]
     *
     * Replacing {nom} string of your message.
     *
     * Remplacent la chaîne {nom} de votre message.
     *
     * @param array $lastnames The array of lastnames
     * @return \Octopush\Message
     */
    public function setRecipientsLastNames(array $lastnames = [])
    {
        $this->params['recipients_last_names'] = implode(',', $lastnames);
        return $this;
    }

    /**
     * The sms_fields_1 param - [Optional]
     *
     * Replacing the string {ch1} of your message.
     *
     * Remplacent la chaîne {ch1} de votre message.
     *
     * @param array $fields The array of ch1 string value
     * @return \Octopush\Message
     */
    public function setSmsFields1(array $fields = [])
    {
        $this->params['sms_fields_1'] = implode(',', $fields);
        return $this;
    }

    /**
     * The sms_fields_2 param - [Optional]
     *
     * Replacing the string {ch2} of your message.
     *
     * Remplacent la chaîne {ch2} de votre message.
     *
     * @param array $fields The array of ch2 string value
     * @return \Octopush\Message
     */
    public function setSmsFields2(array $fields = [])
    {
        $this->params['sms_fields_2'] = implode(',', $fields);
        return $this;
    }

    /**
     * The sms_fields_3 param -	[Optional]
     *
     * Replacing the string {ch3} of your message.
     *
     * Remplacent la chaîne {ch3} de votre message.
     *
     * @param array $fields The array of ch3 string value
     * @return \Octopush\Message
     */
    public function setSmsFields3(array $fields = [])
    {
        $this->params['sms_fields_3'] = implode(',', $fields);
        return $this;
    }
}
