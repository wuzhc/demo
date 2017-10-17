<?php

/**
 * @author tengzhaorong@gmail.com
 * @date 2013-07-25
 */
class DaemonCommand
{

    private $info_dir = "/tmp";
    private $pid_file = "";
    private $terminate = false;
    private $workers_count = 0;
    private $gc_enabled = null;
    private $workers_max = 8;
    public $jobs;

    public function __construct($is_sington = false, $user = 'nobody', $output = "/dev/null")
    {
        $this->is_sington = $is_sington;
        $this->user = $user;
        $this->output = $output;
        $this->checkPcntl();
    }

    /**
     * 检测pcntl扩展
     * @throws Exception
     */
    public function checkPcntl()
    {
        if (!function_exists('pcntl_signal_dispatch')) {
            // PHP < 5.3 uses ticks to handle signals instead of pcntl_signal_dispatch
            // call sighandler only every 10 ticks
            declare(ticks = 10);
        }

        // Make sure PHP has support for pcntl
        if (!function_exists('pcntl_signal')) {
            $message = 'PHP does not appear to be compiled with the PCNTL extension.  This is neccesary for daemonization';
            $this->_log($message);
            throw new Exception($message);
        }

        // 安装信号处理器
        pcntl_signal(SIGTERM, array(__CLASS__, "signalHandler"), false);
        pcntl_signal(SIGINT, array(__CLASS__, "signalHandler"), false);
        pcntl_signal(SIGQUIT, array(__CLASS__, "signalHandler"), false);

        // Enable PHP 5.3 garbage collection
        if (function_exists('gc_enable')) {
            gc_enable();
            $this->gc_enabled = gc_enabled();
        }
    }

    // 守护进程
    public function daemonize()
    {

        global $stdin, $stdout, $stderr;
        global $argv;

        set_time_limit(0);

        // php-cli 模式下运行
        if (php_sapi_name() != "cli") {
            die("only run in command line mode\n");
        }

        // 单例模式
        if ($this->is_sington == true) {
            $this->pid_file = $this->info_dir . "/" . __CLASS__ . "_" . substr(basename($argv[0]), 0, -4) . ".pid";
            $this->checkPidfile();
        }

        umask(0); //���ļ�������0

        if (pcntl_fork() != 0) { //�Ǹ����̣��������˳�
            exit();
        }

        posix_setsid();//�����»Ự�鳤�������ն�

        if (pcntl_fork() != 0) { //�ǵ�һ�ӽ��̣�������һ�ӽ���
            exit();
        }

        chdir("/"); //�ı乤��Ŀ¼

        $this->setUser($this->user) or die("cannot change owner");

        //�رմ򿪵��ļ�������
        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);

        $stdin = fopen($this->output, 'r');
        $stdout = fopen($this->output, 'a');
        $stderr = fopen($this->output, 'a');

        if ($this->is_sington == true) {
            $this->createPidfile();
        }

    }

    /**
     * 检测进程文件
     * @return bool
     */
    public function checkPidfile()
    {
        if (!file_exists($this->pid_file)) {
            return true;
        }
        $pid = file_get_contents($this->pid_file);
        $pid = intval($pid);
        if ($pid > 0 && posix_kill($pid, 0)) {
            $this->_log("the daemon process is already started");
        } else {
            $this->_log("the daemon process end abnormally, please check pidfile " . $this->pid_file);
        }
        exit(1);
    }

    //----����pid
    public function createPidfile()
    {

        if (!is_dir($this->info_dir)) {
            mkdir($this->info_dir);
        }
        $fp = fopen($this->pid_file, 'w') or die("cannot create pid file");
        fwrite($fp, posix_getpid());
        fclose($fp);
        $this->_log("create pid file " . $this->pid_file);
    }

    //�������е��û�
    public function setUser($name)
    {

        $result = false;
        if (empty($name)) {
            return true;
        }
        $user = posix_getpwnam($name);
        if ($user) {
            $uid = $user['uid'];
            $gid = $user['gid'];
            $result = posix_setuid($uid);
            posix_setgid($gid);
        }
        return $result;

    }

    //�źŴ�����
    public function signalHandler($signo)
    {

        switch ($signo) {

            //�û��Զ����ź�
            case SIGUSR1: //busy
                if ($this->workers_count < $this->workers_max) {
                    $pid = pcntl_fork();
                    if ($pid > 0) {
                        $this->workers_count++;
                    }
                }
                break;
            //�ӽ��̽����ź�
            case SIGCHLD:
                while (($pid = pcntl_waitpid(-1, $status, WNOHANG)) > 0) {
                    $this->workers_count--;
                }
                break;
            //�жϽ���
            case SIGTERM:
            case SIGHUP:
            case SIGQUIT:

                $this->terminate = true;
                break;
            default:
                return false;
        }

    }

    /**
     *��ʼ��������
     *$count ׼�������Ľ�����
     */
    public function start($count = 1)
    {

        $this->_log("daemon process is running now");
        // ��һ��������ֹ����ֹͣ����SIGCHLD�źŷ��͸�������
        pcntl_signal(SIGCHLD, array(__CLASS__, "signalHandler"), false); // if worker die, minus children num
        while (true) {
            if (function_exists('pcntl_signal_dispatch')) {

                pcntl_signal_dispatch();
            }

            if ($this->terminate) {
                break;
            }
            $pid = -1;
            if ($this->workers_count < $count) {

                $pid = pcntl_fork();
            }

            if ($pid > 0) {
                // ������
                $this->workers_count++;
            } elseif ($pid == 0) {

                // ������ű�ʾ�ָ�ϵͳ���źŵ�Ĭ�ϴ���
                pcntl_signal(SIGTERM, SIG_DFL);
                pcntl_signal(SIGCHLD, SIG_DFL);
                if (!empty($this->jobs)) {
                    while ($this->jobs['runtime']) {
                        if (empty($this->jobs['argv'])) {
                            call_user_func($this->jobs['function'], $this->jobs['argv']);
                        } else {
                            call_user_func($this->jobs['function']);
                        }
                        $this->jobs['runtime']--;
                        sleep(2);
                    }
                    exit();

                }
                return;

            } else {

                sleep(2);
            }


        }

        $this->mainQuit();
        exit(0);

    }

    //���������˳�
    public function mainQuit()
    {

        if (file_exists($this->pid_file)) {
            unlink($this->pid_file);
            $this->_log("delete pid file " . $this->pid_file);
        }
        $this->_log("daemon process exit now");
        posix_kill(0, SIGKILL);
        exit(0);
    }

    // ��ӹ���ʵ����Ŀǰֻ֧�ֵ���job����
    public function setJobs($jobs = array())
    {

        if (!isset($jobs['argv']) || empty($jobs['argv'])) {

            $jobs['argv'] = "";

        }
        if (!isset($jobs['runtime']) || empty($jobs['runtime'])) {

            $jobs['runtime'] = 1;

        }

        if (!isset($jobs['function']) || empty($jobs['function'])) {

            $this->_log("�����������еĺ�����");
        }

        $this->jobs = $jobs;

    }

    //��־����
    private function _log($message)
    {
        printf("%s\t%d\t%d\t%s\n", date("c"), posix_getpid(), posix_getppid(), $message);
    }

}

// 实例一
$daemon = new DaemonCommand(true);
$daemon->daemonize();
$daemon->start(2);//����2���ӽ��̹���
work();


//���÷���2
$daemon = new DaemonCommand(true);
$daemon->daemonize();
$daemon->setJobs(array('function' => 'work', 'argv' => '', 'runtime' => 2));//function Ҫ���еĺ���,argv���к����Ĳ�����runtime���еĴ���
$daemon->start(2);//����2���ӽ��̹���

//���幦�ܵ�ʵ��
function work()
{
    echo "����1";
}
