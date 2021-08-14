<?php

declare(strict_types=1);

namespace OCA\Cospend\Migration;

use Closure;
use OCA\Cospend\AppInfo\Application;
use OCP\DB\ISchemaWrapper;
use OCP\DB\Types;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\IL10N;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version010314Date20210814170534 extends SimpleMigrationStep {

	/** @var IDBConnection */
	private $connection;
	private $trans;

	/**
	 * @param IDBConnection $connection
	 */
	public function __construct(IDBConnection $connection, IL10N $l10n) {
		$this->connection = $connection;
		$this->trans = $l10n;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('cospend_project_paymentmodes')) {
			$table = $schema->createTable('cospend_project_paymentmodes');
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
			$table->addColumn('projectid', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('name', Types::STRING, [
				'notnull' => false,
				'length' => 300,
			]);
			$table->addColumn('color', Types::STRING, [
				'notnull' => false,
				'length' => 10,
				'default' => null
			]);
			$table->addColumn('encoded_icon', Types::STRING, [
				'notnull' => false,
				'length' => 64,
				'default' => null
			]);
			$table->addColumn('order', Types::INTEGER, [
				'notnull' => true,
				'length' => 4,
				'default' => 0,
			]);
			$table->setPrimaryKey(['id']);
		}

		$table = $schema->getTable('cospend_bills');
		$table->addColumn('paymentmodeid', Types::INTEGER, [
			'notnull' => true,
			'length' => 4,
			'default' => 0,
		]);

		return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
		$qb = $this->connection->getQueryBuilder();

		$ts = (new \DateTime())->getTimestamp();

		// convert pm ids in existing bills
		foreach (Application::PAYMENT_MODE_ID_CONVERSION as $old => $new) {
			$qb->update('cospend_bills')
				->set('paymentmodeid', $qb->createNamedParameter($new, IQueryBuilder::PARAM_INT))
				->set('lastchanged', $qb->createNamedParameter($ts, IQueryBuilder::PARAM_INT))
				->where(
					$qb->expr()->eq('paymentmode', $qb->createNamedParameter($old, IQueryBuilder::PARAM_STR))
				);
			$qb->executeStatement();
			$qb = $qb->resetQueryParts();
		}
	}
}
