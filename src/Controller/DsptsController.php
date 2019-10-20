<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Lib\AbstractFactory\DbFactory;
use App\Lib\AbstractFactory\MockFactory;
use App\Lib\Adapter\DisplaySourceFileImpl;
use App\Lib\Bridge\ExtendedListing;
use App\Lib\Bridge\FileDataSource;
use App\Lib\Bridge\Listing;
use App\Lib\Builder\News;
use App\Lib\Builder\NewsDirector;
use App\Lib\Builder\RssNewsBuilder;
use App\Lib\ChainOfRepository\AlphabetValidationHandler;
use App\Lib\ChainOfRepository\MaxLengthValidationHandler;
use App\Lib\ChainOfRepository\NotNullValidationHandler;
use App\Lib\ChainOfRepository\NumberValidationHandler;
use App\Lib\Command\CompressCommand;
use App\Lib\Command\CopyCommand;
use App\Lib\Command\File;
use App\Lib\Command\Queue;
use App\Lib\Command\TouchCommand;
use App\Lib\Facade\ItemDao;
use App\Lib\Facade\Order;
use App\Lib\Facade\OrderItem;
use App\Lib\Facade\OrderManager;
use App\Lib\Factory\ReaderFactory;
use App\Lib\Iterator\Employee;
use App\Lib\Iterator\Employees;
use App\Lib\Iterator\SalesmanIterator;
use App\Lib\Singleton\SingletonSample;
use App\Lib\Template\ListDisplay;
use App\Lib\Template\TableDisplay;
use Cake\Event\Event;

/**
 * Dspt Controller
 *
 * @method \App\Model\Entity\Article[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DsptsController extends AppController
{
    public function beforeFilter(Event $event)
    {
        $this->Auth->allow();
        return parent::beforeFilter($event);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function template()
    {
        $data = [
            'Design Pattern',
            'Gang of Four',
            'Template Method Sample1',
            'Template Method Sample2'
        ];

        $display1 = new ListDisplay($data);
        $display2 = new TableDisplay($data);

        $display1->display();
        echo '<hr>';
        $display2->display();
        exit;
    }

    public function singleton()
    {
        $instance1 = SingletonSample::getInstance();
        $instance2 = SingletonSample::getInstance();

        echo '<hr>';

        echo 'instance ID : ' . $instance1->getID() . '<br>';
        echo '$instance1->getID() === $instance2->getID() : ' . ($instance1->getID() === $instance2->getID() ? 'true' : 'false');
        echo '<hr>';

        echo '$instance1 === $instance2 : ' . ($instance1 === $instance2 ? 'true' : 'false');
        echo '<hr>';

        try {
            $instance1_clone = clone $instance1;
        } catch (\Exception $e) {
            debug($e->getMessage());
        }
        exit;
    }

    public function adapter()
    {
        $showFile = new DisplaySourceFileImpl(APP . 'Lib/Adapter/ShowFile.php');
        $showFile->display();
        exit;
    }

    public function factory()
    {
        $filename = 'test.xml';
        $factory = new ReaderFactory();
        $data = $factory->create($filename);
        $data->read();
        $data->display();
        exit;
    }

    public function facade()
    {
        $order = new Order();
        $order->addItem(new OrderItem(ItemDao::getInstance()->findById(1), 2));
        $order->addItem(new OrderItem(ItemDao::getInstance()->findById(2), 1));
        $order->addItem(new OrderItem(ItemDao::getInstance()->findById(3), 3));

        OrderManager::order($order);
        exit;
    }

    public function iterator()
    {
        $employees = new Employees();
        $employees->add(new Employee('SMITH', 32, 'CLERK'));
        $employees->add(new Employee('ALLEN', 26, 'SALESMAN'));
        $employees->add(new Employee('MARTIN', 50, 'SALESMAN'));
        $employees->add(new Employee('CLARK', 45, 'MANAGER'));
        $employees->add(new Employee('KING', 58, 'PRESIDENT'));
        $iterator = $employees->getIterator();
        /**
         * iteratorのメソッドを利用する
         */
        echo '<ul>';
        while ($iterator->valid()) {
            $employee = $iterator->current();
            printf('<li>%s (%d, %s)</li>',
                $employee->getName(),
                $employee->getAge(),
                $employee->getJob());

            $iterator->next();
        }
        echo '</ul>';
        echo '<hr>';

        /**
         * foreach文を利用する
         */
        $this->dumpWithForeach($iterator);

        /**
         * 異なるiteratorで要素を取得する
         */
        $this->dumpWithForeach(new SalesmanIterator($iterator));
        exit;
    }

    /**
     * @param \Iterator $iterator
     */
    private function dumpWithForeach(\Iterator $iterator)
    {
        echo '<ul>';
        foreach ($iterator as $employee) {
            printf('<li>%s (%d, %s)</li>',
                $employee->getName(),
                $employee->getAge(),
                $employee->getJob());
        }
        echo '</ul>';
        echo '<hr>';
    }

    public function abstractFactory($factory = null)
    {
        if (empty($factory)) {
            exit;
        }

        switch ($factory) {
            case 1:
                $factory = new DbFactory();
                break;
            case 2:
                $factory = new MockFactory();
                break;
            default:
                throw new \RuntimeException('invalid factory');
        }

        $item_id = 1;
        $item_dao = $factory->createItemDao();
        $item = $item_dao->findById($item_id);
        echo 'ID=' . $item_id . 'の商品は「' . $item->getName() . '」です<br>';

        $order_id = 3;
        $order_dao = $factory->createOrderDao();
        $order = $order_dao->findById($order_id);
        echo 'ID=' . $order_id . 'の注文情報は次の通りです。';
        echo '<ul>';
        foreach ($order->getItems() as $item) {
            echo '<li>' . $item['object']->getName();
        }
        echo '</ul>';
        exit;


    }

    public function bridge()
    {
        $list1 = new Listing(new FileDataSource(APP . 'Lib/Bridge/data.txt'));
        $list2 = new ExtendedListing(new FileDataSource(APP . 'Lib/Bridge/data.txt'));

        try {
            $list1->oepn();
            $list2->oepn();
        } catch (\Exception $e) {
            die($e->getMessage());
        }

        $data = $list1->read();
        echo $data;

        $data = $list2->readWithEncode();
        echo $data;

        $list1->close();
        $list2->close();

        exit;
    }

    public function builder()
    {
        $builder = new RssNewsBuilder();
        $url = 'https://pear.php.net/feeds/latest.rss';

        $director = new NewsDirector($builder, $url);
        foreach ($director->getNews() as $article) {
            printf('<li>[%s] <a href="%s">%s</a></li>',
                $article->getData(),
                $article->getUrl(),
                htmlspecialchars($article->getTitle(), ENT_QUOTES, mb_internal_encoding())
            );
        }
        exit;
    }

    public function chainOfRepository($validation_type = null, $input = null)
    {
        $not_null_handler = new NotNullValidationHandler();
        $length_handler = new MaxLengthValidationHandler(8);
        switch ($validation_type) {
            case 1:
                $option_handler = new AlphabetValidationHandler();
                break;
            case 2:
                $option_handler = new NumberValidationHandler();
                break;
        }

        $length_handler->setHandler($option_handler);
        $handler = $not_null_handler->setHandler($length_handler);

        /**
         * 処理実行と結果メッセージの表示
         */
        $result = $handler->validate($input);
        if ($result === false) {
            echo '検証できませんでした';
        } elseif (is_string($result) && $result !== '') {
            echo '<p style="color: #dd0000;">' . $result . '</p>';
        } else {
            echo '<p style="color: #008800;">OK</p>';
        }
        exit;
    }

    public function command()
    {
        $q = new Queue();
        $file = new File('sample.txt');
        $q->addCommand(new TouchCommand($file));
        $q->addCommand(new CompressCommand($file));
        $q->addCommand(new CopyCommand($file));

        $q->run();
        exit;
    }
}
