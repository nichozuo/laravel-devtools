<?php


namespace Nichozuo\LaravelDevtools\Commands;


use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Nichozuo\LaravelDevtools\Helper\DbalHelper;
use Nichozuo\LaravelDevtools\Helper\GenHelper;
use Nichozuo\LaravelDevtools\Helper\TableHelper;

class DumpTableCommand extends Command
{
    protected $signature = 'dt {table}';
    protected $description = 'dump the fields of the table';

    /**
     * @throws Exception
     */
    public function handle()
    {
        DbalHelper::register();

        $tableName = (string)Str::of($this->argument('table'))->snake()->plural();
        $table = TableHelper::GetTable($tableName);
        $columns = TableHelper::GetTableColumns($table);

        if ($table == '') {
            $this->line('Please Input Table Name');
        } else {
            $this->warn('生成 Table 模板');
            $this->line(GenHelper::GenTableString($table));
            $this->line(GenHelper::GenCommentString($table));
            $this->line(GenHelper::GenFillableString($columns));

            $this->warn('生成 Validate 模板');
            $this->line(GenHelper::GenRequestValidateString($columns));

            $this->warn('生成 Insert 模板');
            $this->line(GenHelper::GenInsertString($columns));

//            $this->warn('生成 Annotation 模板');
//            $this->line(GenHelper::genAnnotationString($columns));
        }
    }
}