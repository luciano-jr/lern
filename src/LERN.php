<?php

namespace Tylercd100\LERN;

use Exception;
use Monolog\Handler\HandlerInterface;
use Tylercd100\LERN\Components\Notifier;
use Tylercd100\LERN\Components\Recorder;

/**
* The master class
*/
class LERN 
{
    /**
     * @var Exception
     */
    private $exception;

    /**
     * @var \Tylercd100\LERN\Components\Notifier
     */
    private $notifier;

    /**
     * @var \Tylercd100\LERN\Components\Recorder
     */
    private $recorder;
    
    /**
     * @param \Tylercd100\LERN\Components\Notifier|null $notifier Notifier instance
     * @param \Tylercd100\LERN\Components\Recorder|null $recorder Recorder instance
     */
    public function __construct(Notifier $notifier = null, Recorder $recorder = null)
    {
        if (empty($notifier)) {
            $notifier = new Notifier();
        }
        $this->notifier = $notifier;

        if (empty($recorder)) {
            $recorder = new Recorder();
        }
        $this->recorder = $recorder;
    }

    /**
     * Will execute record and notify methods
     * @param  Exception $e   The exception to use
     * @return ExceptionModel the recorded Eloquent Model
     */
    public function handle(Exception $e)
    {
        $this->exception = $e;
        $this->notify($e);
        return $this->record($e);
    }

    /**
     * Stores the exception in the database
     * @param  Exception $e   The exception to use
     * @return \Tylercd100\LERN\Models\ExceptionModel|false The recorded Exception as an Eloquent Model
     */
    public function record(Exception $e)
    {
        $this->exception = $e;
        return $this->recorder->record($e);
    }

    /**
     * Will send the exception to all monolog handlers
     * @param  Exception $e The exception to use
     * @return void
     */
    public function notify(Exception $e)
    {
        $this->exception = $e;
        $this->notifier->send($e);
    }

    /**
     * Pushes on another Monolog Handler
     * @param  HandlerInterface $handler The handler instance to add on
     * @return $this
     */
    public function pushHandler(HandlerInterface $handler) {
        $this->notifier->pushHandler($handler);
        return $this;
    }

    /**
     * Get Notifier
     * @return \Tylercd100\LERN\Components\Notifier 
     */
    public function getNotifier()
    {
        return $this->notifier;
    }

    /**
     * Set Notifier
     * @param \Tylercd100\LERN\Components\Notifier $notifier A Notifier instance to use
     * @return \Tylercd100\LERN\LERN
     */
    public function setNotifier(Notifier $notifier)
    {
        $this->notifier = $notifier;
        return $this;
    }

    /**
     * Get Recorder
     * @return \Tylercd100\LERN\Components\Recorder 
     */
    public function getRecorder()
    {
        return $this->recorder;
    }

    /**
     * Set Recorder
     * @param \Tylercd100\LERN\Components\Recorder $recorder A Recorder instance to use
     * @return \Tylercd100\LERN\LERN
     */
    public function setRecorder(Recorder $recorder)
    {
        $this->recorder = $recorder;
        return $this;
    }

    /**
     * Set a string or a closure to be called that will generate the message body for the notification
     * @param function|string $cb This closure function will be passed an Exception and must return a string
     * @return $this
     */
    public function setMessage($cb)
    {
        $this->notifier->setMessage($cb);
        return $this;
    }

    /**
     * Set a string or a closure to be called that will generate the subject line for the notification
     * @param function|string $cb This closure function will be passed an Exception and must return a string
     * @return $this
     */
    public function setSubject($cb)
    {
        $this->notifier->setSubject($cb);
        return $this;
    }

}