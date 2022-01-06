<?php


namespace Nichozuo\LaravelDevtools\Commands;


use Doctrine\DBAL\Schema\Table;
use Exception;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Str;
use Nichozuo\LaravelDevtools\Helper\DbalHelper;
use Nichozuo\LaravelDevtools\Helper\GenHelper;
use Nichozuo\LaravelDevtools\Helper\StubHelper;
use Nichozuo\LaravelDevtools\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GenFilesCommand extends BaseCommand
{
    protected $name = 'gf';
    protected $description = 'Generate files of the table';

    protected function getArguments(): array
    {
        return [
            ['table', InputArgument::REQUIRED, '表名'],
            ['module', InputArgument::OPTIONAL, '模块名'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['migration', 'm', InputOption::VALUE_NONE, '创建 migration 文件'],
            ['model', 'd', InputOption::VALUE_NONE, '创建 model 文件'],
            ['factory', 'f', InputOption::VALUE_NONE, '创建 factory 文件'],
            ['seed', 's', InputOption::VALUE_NONE, '创建 seed 文件'],
            ['controller', 'c', InputOption::VALUE_NONE, '创建 controller 文件'],
            ['force', 'F', InputOption::VALUE_NONE, '强制创建并覆盖'],
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function handle()
    {
        DbalHelper::register();

        $options = $this->options();

        $tableName = (string)Str::of($this->argument('table'))->snake()->plural();
        $moduleName = (string)Str::of($this->argument('module'))->studly();
        $modelName = (string)Str::of($tableName)->studly();

        $table = TableHelper::GetTable($tableName);
        $columns = TableHelper::GetTableColumns($table);

        $this->makeMigration($tableName, $options);
        $this->makeModel($table, $columns, $modelName, $options);
        $this->makeFactory($modelName, $options);
        $this->makeSeed($modelName, $options);
        $this->makeController($table, $columns, $modelName, $moduleName, $options);
    }

    /**
     * @param string $tableName
     * @param array $options
     */
    private function makeMigration(string $tableName, array $options)
    {
        if ($options['migration']) {
            try {
                $this->call('make:migration', [
                    'name' => "create_{$tableName}_table",
                    '--create' => $tableName,
                    '--table' => $tableName,
                ]);
            } catch (Exception $ex) {
                $this->error($ex->getMessage());
            }
        }
    }

    /**
     * @param Table $table
     * @param array $columns
     * @param string $modelName
     * @param array $options
     */
    private function makeModel(Table $table, array $columns, string $modelName, array $options)
    {
        if ($options['model']) {
            $hasSoftDelete = TableHelper::HasSoftDelete($table->getColumns());
            $stubName = $hasSoftDelete ? 'modelWithSoftDelete.stub' : 'model.stub';
            $stubContent = StubHelper::GetStub($stubName);
            $stubContent = StubHelper::Replace([
                '{{ModelName}}' => $modelName,
                '{{TableString}}' => GenHelper::GenTableString($table),
                '{{CommentString}}' => GenHelper::GenCommentString($table),
                '{{FillableString}}' => GenHelper::GenFillableString($columns),
            ], $stubContent);
            $filePath = $this->laravel['path'] . '/Models/' . $modelName . '.php';
            StubHelper::Save($filePath, $stubContent);
        }
    }

    /**
     * @param string $modelName
     * @param array $options
     */
    private function makeFactory(string $modelName, array $options)
    {
        if ($options['factory']) {
            try {
                $this->call('make:factory', [
                    'name' => "{$modelName}Factory"
                ]);
            } catch (Exception $ex) {
                $this->error($ex->getMessage());
            }
        }
    }

    /**
     * @param string $modelName
     * @param array $options
     */
    private function makeSeed(string $modelName, array $options)
    {
        if ($options['seed']) {
            try {
                $this->call('make:seed', [
                    'name' => "{$modelName}TableSeeder"
                ]);
            } catch (Exception $ex) {
                $this->error($ex->getMessage());
            }
        }
    }

    /**
     * @param Table $table
     * @param array $columns
     * @param string $modelName
     * @param string $moduleName
     * @param array $options
     */
    private function makeController(Table $table, array $columns, string $modelName, string $moduleName, array $options)
    {
        if ($options['controller']) {
            $hasSoftDelete = TableHelper::HasSoftDelete($table->getColumns());
            $stubName = $hasSoftDelete ? 'controllerWithSoftDelete.stub' : 'controller.stub';
            $stubContent = StubHelper::GetStub($stubName);
            $stubContent = StubHelper::Replace([
                '{{ModelName}}' => $modelName,
                '{{TableComment}}' => $table->getComment(),
                '{{ModuleName}}' => $moduleName,
                '{{InsertString}}' => GenHelper::GenRequestValidateString($columns, "\t\t\t"),
            ], $stubContent);
            $filePath = $this->laravel['path'] . "/Modules/{$moduleName}/{$modelName}Controller.php";
            StubHelper::Save($filePath, $stubContent);
        }
    }
}