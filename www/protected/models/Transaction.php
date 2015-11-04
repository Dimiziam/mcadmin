<?php

/**
 * Transaction
 *
 * Model for table transactions
 *
 * @package MediaClub
 * @subpackage models
 * @copyright (C) 2014 point.od.ua <support@point.od.ua>
 * @author Tishenko Vladimir <vagrand@mail.ru>
 */
class Transaction extends InfoModel
{
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 * @return Resource the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Method return table name for current model
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'transactions';
	}

	/**
	 * Returns the primary key of the associated database table.
	 *
	 * @return string
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * Method return field rules
	 *
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			// Required rules
			array('type, amount', 'required', 'on' => 'add_money'),
		);
	}

	/**
	 * Method return attribute labels
	 *
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'type' => MTLang::t('type', 'transaction'),
			'amount' => MTLang::t('amount', 'transaction')
		);
	}

	/**
	 * Method to search transactions
	 *
	 * @param array $params
	 * @return mixed
	 */
	public function search($params)
	{
		//pc::debug($params);
		//pc::debug($this->attributes);

		// Create query command
		$command = $this->getDbConnection()->createCommand(
			'SELECT SQL_CALC_FOUND_ROWS transactions.*, users.email,users.download_all,users.country_code
			FROM ' . $this->tableName() . ' transactions
				INNER JOIN ' . User::model()->tableName() . ' users ON users.id = transactions.user_id
			WHERE 1
				' . (!empty($params['id']) ? ' and transactions.id = :id' : '') . '
				' . (!empty($params['user_id']) ? ' and transactions.user_id = :user_id ' : '') . '
				' . (!empty($params['billing']) ? ' and transactions.billing = :billing ' : '') . '
				' . (!empty($params['status']) ? ' and transactions.status = :status ' : '') . '
			ORDER BY transactions.id DESC
			LIMIT ' . ($this->getCurrentPage() * $this->getRowsPerPage()) . ', ' . $this->getRowsPerPage()
		);

		// Bind params
		if (!empty($params['id'])) {
			$command->bindParam(":id", $params['id'], PDO::PARAM_STR);
		}
		if (!empty($params['user_id'])) {
			$command->bindParam(":user_id", $params['user_id'], PDO::PARAM_STR);
		}

		if (!empty($params['billing'])) {
			$command->bindParam(":billing", $params['billing'], PDO::PARAM_STR);
		}


        if (!empty($params['status'])) {
            $command->bindParam(":status", $params['status'], PDO::PARAM_STR);
        }

		// Try to find transactions
		$transactions = $command->queryAll();

		// Check transactions
		if (empty($transactions)) {
			return false;
		}

		// Get nuber of found rows
		$foundRows = $this->getDbConnection()->createCommand('SELECT found_rows() as totalRows')->queryScalar();

		// Get data provider
		$provider = new MTArrayDataProvider($transactions,
			array(
				'totalItemCount' => $foundRows,
				'pagination' => array('pageSize' => $this->getRowsPerPage()),
			)
		);

		// Set offset
		$provider->setOffset(0);

		return $provider;
	}

	/**
	 * Return total succsessful transaction
	 * @param $userId
	 * @return array
	 */
	public function successfulTransaction($userId) {
		$transaction=array();

		$command = $this->getDbConnection()->createCommand(
			'SELECT billing, count(*) as total
			FROM ' . $this->tableName() . ' transactions
			WHERE 	user_id = :user_id
					and type=:type
					and status=:status
					and billing!=:billing
			GROUP by billing
			');

		//params
		$type='add';
		$status='done';
		$billing='direct';

		$command->bindParam(":user_id", $userId, PDO::PARAM_INT);
		$command->bindParam(":type", $type, PDO::PARAM_STR);
		$command->bindParam(":status", $status, PDO::PARAM_STR);
		$command->bindParam(":billing", $billing, PDO::PARAM_STR);


		return $command->queryAll();;
	}

	/**
	 * Method to cleare current model cache
	 *
	 * @param int $userId
	 * @return bool
	 */
	public function cleareCache($userId)
	{
		TRedis::instance('data')->deleteByTag('TRANSACTIONS_' . $userId);
		User::model()->cleareCache($userId);
	}

	/**
	 * After delete hook
	 *
	 * @return void
	 */
	public function afterDelete()
	{
		// Remove all cache
		$this->cleareCache($this->user_id);

	}

	/**
	 * Method to save model
	 *
	 * @param bool $runValidation
	 * @param array $attributes
	 */
	public function save($runValidation = true, $attributes = null)
	{
		// Call parent method
		$result = parent::save($runValidation, $attributes);

		// Check result
		if ($result === true) {
			// Remove all cache
			$this->cleareCache($this->user_id);
		}

		return $result;
	}


}
