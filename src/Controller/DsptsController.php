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
use App\Lib\Decorator\DoubleByteText;
use App\Lib\Decorator\PlainText;
use App\Lib\Decorator\UpperCaseText;
use App\Lib\Facade\ItemDao;
use App\Lib\Facade\Order;
use App\Lib\Facade\OrderItem;
use App\Lib\Facade\OrderManager;
use App\Lib\Factory\ReaderFactory;
use App\Lib\FlyWeight\Item;
use App\Lib\FlyWeight\ItemFactory;
use App\Lib\Interpreter\Context;
use App\Lib\Interpreter\JobCommand;
use App\Lib\Iterator\Employee;
use App\Lib\Iterator\Employees;
use App\Lib\Iterator\SalesmanIterator;
use App\Lib\Singleton\SingletonSample;
use App\Lib\Template\ListDisplay;
use App\Lib\Template\TableDisplay;
use App\Lib\Composite\Employee as Emp;
use App\Lib\Composite\Group;
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
        $factory  = new ReaderFactory();
        $data     = $factory->create($filename);
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

        $item_id  = 1;
        $item_dao = $factory->createItemDao();
        $item     = $item_dao->findById($item_id);
        echo 'ID=' . $item_id . 'の商品は「' . $item->getName() . '」です<br>';

        $order_id  = 3;
        $order_dao = $factory->createOrderDao();
        $order     = $order_dao->findById($order_id);
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
        $url     = 'https://pear.php.net/feeds/latest.rss';

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
        $length_handler   = new MaxLengthValidationHandler(8);
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
        $q    = new Queue();
        $file = new File('sample.txt');
        $q->addCommand(new TouchCommand($file));
        $q->addCommand(new CompressCommand($file));
        $q->addCommand(new CopyCommand($file));

        $q->run();
        exit;
    }

    public function composite()
    {
        $root_entry = new Group("001", "本社");
        $root_entry->add(new Emp("00101", "CEO"));
        $root_entry->add(new Emp("00102", "CTO"));

        $group1 = new Group("010", "○○支店");
        $group1->add(new Emp("01001", "支店長"));
        $group1->add(new Emp("01002", "佐々木"));
        $group1->add(new Emp("01003", "鈴木"));
        $group1->add(new Emp("01003", "吉田"));

        $group2 = new Group("110", "△△営業所");
        $group2->add(new Emp("11001", "川村"));
        $group1->add($group2);
        $root_entry->add($group1);

        $group3 = new Group("020", "××支店");
        $group3->add(new Emp("02001", "萩原"));
        $group3->add(new Emp("02002", "田島"));
        $group3->add(new Emp("02002", "白井"));
        $root_entry->add($group3);

        $root_entry->dump();
        exit;
    }

    public function decorator()
    {
        $data     = $this->request->getData();
        $text     = $data['text'] ?? '';
        $decorate = $data['decorator'] ?? [];
        if ($text !== '') {
            $text_object = new PlainText();
            $text_object->setText($text);

            foreach ($decorate as $k => $val) {
                switch ($val) {
                    case 'double':
                        $text_object = new DoubleByteText($text_object);
                        break;
                    case 'upper':
                        $text_object = new UpperCaseText($text_object);
                        break;
                    default:
                        throw new \RuntimeException('invalid decorator');
                }
            }
            $text = h($text_object->getText()) . "<br>";
        }
        $this->set('text', $text);
    }

    public function flyWeight()
    {
        $factory = ItemFactory::getInstance(APP . 'Lib/FlyWeight/data.txt');

        $items = [];
        $items[] = $factory->getItem('ABC0001');
        $items[] = $factory->getItem('ABC0002');
        $items[] = $factory->getItem('ABC0003');

        if ($items[0] === $factory->getItem('ABC0001')) {
            echo '同一のオブジェクトです';
        } else {
            echo '同一のオブジェクトではありません';
        }

        echo '<dl>';
        foreach ($items as $object) {
            echo '<dt>' . htmlspecialchars($object->getName(), ENT_QUOTES, mb_internal_encoding()) . '</dt>';
            echo '<dd>商品番号：' . $object->getCode() . '</dd>';
            echo '<dd>\\' . number_format((int)$object->getPrice()) . '-</dd>';
        }
        echo '</dl>';
        exit;
    }

    public function interpreter()
    {
        $command = 'begin date line date line date line diskspace end';
        if ($command !== '') {
            $job = new JobCommand();
            try {
                $job->execute(new Context($command));
            } catch (\Exception $e) {
                echo htmlspecialchars($e->getMessage(), ENT_QUOTES, mb_internal_encoding());
            }
            echo '<hr>';
        }
        exit;
    }
}
