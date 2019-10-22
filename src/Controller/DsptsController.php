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
use App\Lib\Mediator\Chatroom;
use App\Lib\Mediator\User;
use App\Lib\Memento\Data;
use App\Lib\Memento\DataCaretaker;
use App\Lib\Observer\Cart;
use App\Lib\Observer\LoggingListener;
use App\Lib\Observer\PresentListener;
use App\Lib\Prototype\DeepCopyItem;
use App\Lib\Prototype\ItemManager;
use App\Lib\Prototype\ShallowCopyItem;
use App\Lib\Proxy\DbItemDao;
use App\Lib\Proxy\ItemDaoProxy;
use App\Lib\Proxy\MockItemDao;
use App\Lib\Singleton\SingletonSample;
use App\Lib\Strategy\ItemDataContext;
use App\Lib\Strategy\ReadFixedLengthDataStrategy;
use App\Lib\Strategy\ReadTabSeparatedDataStrategy;
use App\Lib\Template\ListDisplay;
use App\Lib\Template\TableDisplay;
use App\Lib\Composite\Employee as Emp;
use App\Lib\Composite\Group;
use App\Lib\Visitor\CountVisitor;
use App\Lib\Visitor\DumpVisitor;
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

    public function mediator()
    {
        $chatroom = new Chatroom();

        $sasaki = new User('佐々木');
        $suzuki = new User('鈴木');
        $yoshida = new User('吉田');
        $kawamura = new User('川村');
        $tajima = new User('田島');

        $chatroom->login($sasaki);
        $chatroom->login($suzuki);
        $chatroom->login($yoshida);
        $chatroom->login($kawamura);
        $chatroom->login($tajima);

        $sasaki->sendMessage('鈴木', '来週の予定は？') ;
        $suzuki->sendMessage('川村', '秘密です') ;
        $yoshida->sendMessage('萩原', '元気ですか？') ;
        $tajima->sendMessage('佐々木', 'お邪魔してます') ;
        $kawamura->sendMessage('吉田', '私事で恐縮ですが…') ;
        exit;
    }

    public function memento($mode = null, $comment = null)
    {
        $caretaker = new DataCaretaker();
        $data = $_SESSION['data'] ?? new Data();

        switch ($mode) {
            case 'add':
                $data->addComment($comment ?? '');
                break;
            case 'save':
                $caretaker->setSnapshot($data->takeSnapshot());
                echo '<font style="color: #dd0000;">データを保存しました。</font><br>';
                break;
            case 'restore':
                $data->restoreSnapshot($caretaker->getSnapshot());
                echo '<font style="color: #00aa00;">データを復元しました。</font><br>';
                break;
            case 'clear':
                $data = new Data();
        }

        /**
         * 登録したコメントを表示する
         */
        echo '今までのコメント';
        if (!is_null($data)) {
            echo '<ol>';
            foreach ($data->getComment() as $comment) {
                echo '<li>'
                    . htmlspecialchars($comment, ENT_QUOTES, mb_internal_encoding())
                    . '</li>';
            }
            echo '</ol>';
        }
        $_SESSION['data'] = $data;
        exit;
    }

    public function observer($item = '', $mode = '')
    {
        session_start();

        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : null;
        if (is_null($cart)) {
            $cart = new Cart();
            $cart->addListener(new PresentListener());
            $cart->addListener(new LoggingListener());
        }

        switch ($mode) {
            case 'add':
                echo '<p style="color: #aa0000">追加しました</p>';
                $cart->addItem($item);
                break;
            case 'remove':
                echo '<p style="color: #008800">削除しました</p>';
                $cart->removeItem($item);
                break;
            case 'clear':
                echo '<p style="color: #008800">クリアしました</p>';
                $cart = new Cart();
                $cart->addListener(new PresentListener());
                $cart->addListener(new LoggingListener());
                break;
        }

        $_SESSION['cart'] = $cart;

        echo '<h1>商品一覧</h1>';
        echo '<ul>';
        foreach ($cart->getItems() as $item_name => $quantity) {
            echo '<li>' . $item_name . ' ' . $quantity . '個</li>';
        }
        exit;
    }

    public function prototype()
    {
        $manager = new ItemManager();

        $item = new DeepCopyItem('ABC0001', 'TTT', 3800);
        $detail = new \stdClass();
        $detail->comment = 'comment test';
        $item->setDetail($detail);
        $manager->registItem($item);

        $item = new ShallowCopyItem('ABC0002', 'ぬいぐるみ', 1500);
        $detail = new \stdClass();
        $detail->comment = '商品Bのコメントです';
        $item->setDetail($detail);
        $manager->registItem($item);

        $this->testCopy($manager, 'ABC0001');
        $this->testCopy($manager, 'ABC0002');
        exit;
    }

    private function testCopy(ItemManager $manager, $item_code)
    {
        /**
         * 商品のインスタンスを2つ作成
         */
        $item1 = $manager->create($item_code);
        $item2 = $manager->create($item_code);

        /**
         * 1つだけコメントを削除
         */
        $item2->getDetail()->comment = 'コメントを書き換えました';

        /**
         * 商品情報を表示
         * 深いコピーをした場合、$item2への変更は$item1に影響しない
         */
        echo '■オリジナル';
        $item1->dumpData();
        echo '■コピー';
        $item2->dumpData();
        echo '<hr>';
    }

    public function proxy($dao = null, $proxy = null)
    {
        if (isset($dao) && isset($proxy)) {
            switch ($dao) {
                case 1:
                    $dao = new MockItemDao();
                    break;
                default:
                    $dao = new DbItemDao();
                    break;
            }

            switch ($proxy) {
                case 1:
                    $dao = new ItemDaoProxy($dao);
                    break;
            }

            for ($item_id = 1; $item_id <= 3; $item_id++) {
                $item = $dao->findById($item_id);
                echo 'ID=' . $item_id . 'の商品は「' . $item->getName() . '」です<br>';
            }

            /**
             * 再度データを取得
             */
            $item = $dao->findById(2);
            echo 'ID=' . $item_id . 'の商品は「' . $item->getName() . '」です<br>';
        }
        exit;
    }

    public function state()
    {
        session_start();

        $context = isset($_SESSION['context']) ? $_SESSION['context'] : null;
        if (is_null($context)) {
            $context = new \App\Lib\State\User('ほげ');
        }

        $mode = (isset($_GET['mode']) ? $_GET['mode'] : '');
        switch ($mode) {
            case 'state':
                echo '<p style="color: #aa0000">状態を遷移します</p>';
                $context->switchState();
                break;
            case 'inc':
                echo '<p style="color: #008800">カウントアップします</p>';
                $context->incrementCount();
                break;
            case 'reset':
                echo '<p style="color: #008800">カウントをリセットします</p>';
                $context->resetCount();
                break;
        }

        $_SESSION['context'] = $context;

        echo 'ようこそ、' . $context->getUserName() . 'さん<br>';
        echo '現在、ログインして' . ($context->isAuthenticated() ? 'います' : 'いません') . '<br>';
        echo '現在のカウント：' . $context->getCount() . '<br>';
        echo $context->getMenu() . '<br>';
        exit;
    }

    public function strategy()
    {
        $strategy1 = new ReadFixedLengthDataStrategy(APP . 'Lib/Strategy/fixed_length_data.txt');
        $context1 = new ItemDataContext($strategy1);
        $this->dumpData($context1->getItemData());

        echo '<hr>';

        /**
         * タブ区切りデータを読み込む
         */
        $strategy2 = new ReadTabSeparatedDataStrategy(APP . 'Lib/Strategy/tab_separated_data.txt');
        $context2 = new ItemDataContext($strategy2);
        $this->dumpData($context2->getItemData());
        exit;
    }

    private function dumpData($data)
    {
        echo '<dl>';
        foreach ($data as $object) {
            echo '<dt>' . $object->item_name . '</dt>';
            echo '<dd>商品番号：' . $object->item_code . '</dd>';
            echo '<dd>\\' . number_format($object->price) . '-</dd>';
            echo '<dd>' . date('Y/m/d', $object->release_date) . '発売</dd>';
        }
        echo '</dl>';
    }

    public function visitor()
    {
        $root_entry = new \App\Lib\Visitor\Group("001", "本社");
        $root_entry->add(new \App\Lib\Visitor\Employee("00101", "CEO"));
        $root_entry->add(new \App\Lib\Visitor\Employee("00102", "CTO"));

        $group1 = new \App\Lib\Visitor\Group("010", "○○支店");
        $group1->add(new \App\Lib\Visitor\Employee("01001", "支店長"));
        $group1->add(new \App\Lib\Visitor\Employee("01002", "佐々木"));
        $group1->add(new \App\Lib\Visitor\Employee("01003", "鈴木"));
        $group1->add(new \App\Lib\Visitor\Employee("01003", "吉田"));

        $group2 = new \App\Lib\Visitor\Group("110", "△△営業所");
        $group2->add(new \App\Lib\Visitor\Employee("11001", "川村"));
        $group1->add($group2);
        $root_entry->add($group1);

        $group3 = new \App\Lib\Visitor\Group("020", "××支店");
        $group3->add(new \App\Lib\Visitor\Employee("02001", "萩原"));
        $group3->add(new \App\Lib\Visitor\Employee("02002", "田島"));
        $group3->add(new \App\Lib\Visitor\Employee("02002", "白井"));
        $root_entry->add($group3);

        /**
         * 木構造をダンプ
         */
        $root_entry->accept(new DumpVisitor());

        /**
         * 同じ木構造に対して、別のVisitorを使用する
         */
        $visitor = new CountVisitor();
        $root_entry->accept($visitor);
        echo '組織数：' . $visitor->getGroupCount() . '<br>';
        echo '社員数：' . $visitor->getEmployeeCount() . '<br>';
        exit;
    }
}
