<?php
use Migrations\AbstractMigration;

class CreateGames extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('games');
//        $table->changeColumn('id', 'string');
        $table->addColumn('board', 'string',[
            'default' => '---------',
            'null' => false
        ]);
        $table
            ->addColumn('status', 'enum', [
                'values' => ['RUNNING', 'X_WON', 'O_WON', 'DRAW'],
                'default' => 'RUNNING'
            ]);
        $table->create();
        $table = $this->table('games');
        $table->changeColumn('id', 'string');
        $table->update();
    }
}
