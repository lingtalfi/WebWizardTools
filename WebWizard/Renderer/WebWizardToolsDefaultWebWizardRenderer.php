<?php


namespace Ling\WebWizardTools\WebWizard\Renderer;


use Ling\WebWizardTools\WebWizard\WebWizardToolsWebWizard;

/**
 * The WebWizardToolsDefaultWebWizardRenderer class.
 */
class WebWizardToolsDefaultWebWizardRenderer implements WebWizardToolsWebWizardRendererInterface
{


    /**
     * Whether to call the jquery library.
     *
     * By default, it's false, meaning it's assumed that you call jquery yourself.
     *
     * @var bool = false
     */
    protected $includeJquery;


    /**
     * Builds the WebWizardToolsDefaultWebWizardRenderer instance.
     */
    public function __construct()
    {
        $this->includeJquery = false;
    }

    /**
     * Sets the includeJquery.
     *
     * @param bool $includeJquery
     */
    public function setIncludeJquery(bool $includeJquery)
    {
        $this->includeJquery = $includeJquery;
    }


    /**
     * @implementation
     */
    public function render(WebWizardToolsWebWizard $wizard)
    {


        $processes = $wizard->getProcesses();
        $processKeyName = $wizard->getProcessKeyName();
        $triggerExtraParams = $wizard->getTriggerExtraParams();

        $executedProcess = $wizard->run();


        ?>
        <?php if (null !== $executedProcess):
        $report = $executedProcess->getReport();
        $isSuccessful = $report->isSuccessful();
        $traceMessages = $report->getTraceMessages();
        $infoMessages = $report->getInfoMessages();
        $importantMessages = $report->getImportantMessages();

        ?>


        <style>
            .error {
                color: red;
            }


            .success {
                color: green;
            }


            .info-msg {
                color: blue;
            }

            .important-msg {
                color: orange;
            }

            .trace-msg {
                color: gray;
            }

            .error-msg, .exception-msg {
                color: red;
            }


            .process-table {
                border-collapse: collapse;
            }

            .process-table, .process-table tr, .process-table td {
                border: 1px solid #ddd;
            }

            .process-table td {
                padding: 4px;
            }

            .process-disabled span {
                color: #aaa;
            }

            .line-title, .result {
                border-bottom: 2px solid #6a6a6a;
                padding-bottom: 5px;
            }

            .result{
                margin-bottom: 10px;
            }


        </style>

        <div class="result">

            <h3 class="line-title">Report
                <?php if (true === $isSuccessful): ?>
                    <span class="success">(success)</span>
                <?php else: ?>
                    <span class="error">(error)</span>
                <?php endif; ?>
            </h3>
            <?php if (false === $isSuccessful):
                $errors = $report->getErrorMessages();
                $exceptions = $report->getExceptionMessages();

                ?>

                <?php if ($errors): ?>
                <h4>Errors (<?php echo count($errors); ?>)</h4>
                <p>
                    The following errors occurred:
                </p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li class="error-msg"><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>


                <?php if ($exceptions): ?>
                <h4>Exceptions (<?php echo count($exceptions); ?>)</h4>
                <p>
                    The following exceptions occurred:
                </p>
                <ul>
                    <?php foreach ($exceptions as $exception): ?>
                        <li class="exception-msg"><?php echo $exception; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php endif; ?>


            <h4>Info messages (<?php echo count($infoMessages); ?>)</h4>
            <?php if ($infoMessages): ?>
                <ul>
                    <?php foreach ($infoMessages as $infoMessage): ?>
                        <li class="info-msg"><?php echo $infoMessage; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if ($importantMessages): ?>
                <h4>Important messages (<?php echo count($importantMessages); ?>)</h4>
                <ul>
                    <?php foreach ($importantMessages as $importantMessage): ?>
                        <li class="important-msg"><?php echo $importantMessage; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>


            <?php if ($traceMessages): ?>
                <div>
                    <h4>Trace messages (<?php echo count($traceMessages); ?>)
                        <button id="toggle-trace-messages">Show/Hide trace messages</button>
                    </h4>
                    <div id="trace-message-container" style="display: none">
                        <ul>
                            <?php foreach ($traceMessages as $msg): ?>
                                <li class="trace-msg"><?php echo $msg; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>


        </div>
    <?php endif; // null !== $executedProcess
        ?>


        <?php if (null !== $executedProcess && true === $isSuccessful): ?>
        <?php echo $wizard->getOnProcessSuccessMessage(); ?>

    <?php else: ?>


        <div class="tasklist">
            <h3>Available processes</h3>
            <table class="process-table">

                <tr>
                    <td>Label</td>
                    <td>Learn more</td>
                    <td>Disabled reason</td>
                </tr>
                <?php foreach ($processes as $process):
                    $isEnabled = $process->isEnabled();
                    $sClass = (true === $isEnabled) ? "process-enabled" : "process-disabled";
                    ?>
                    <tr class="<?php echo $sClass; ?>">
                        <td>
                            <?php if (true === $isEnabled): ?>
                                <form method="post" action="#">
                                    <a class="wwt-trigger" href="#">
                                        <?php echo $process->getLabel(); ?>
                                    </a>
                                    <input type="hidden" name="<?php echo htmlspecialchars($processKeyName); ?>"
                                           value="<?php echo htmlspecialchars($process->getName()); ?>"/>
                                    <?php foreach ($triggerExtraParams as $k => $v): ?>
                                        <input type="hidden" name="<?php echo htmlspecialchars($k); ?>"
                                               value="<?php echo htmlspecialchars($v); ?>"/>
                                    <?php endforeach; ?>
                                </form>
                            <?php else: ?>
                                <span><?php echo $process->getLabel(); ?></span>
                            <?php endif; ?>


                        </td>
                        <td>
                            <?php echo $process->getLearnMore(); ?>
                        </td>
                        <td>
                            <?php echo $process->getDisabledReason(); ?>
                        </td>
                        <?php ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>


        <?php if (true === $this->includeJquery): ?>
        <script src="/libs/universe/Ling/Jquery/3.5.1/jquery.min.js"></script>
    <?php endif; ?>


        <script>
            document.addEventListener("DOMContentLoaded", function (event) {
                $(document).ready(function () {
                    $('.wwt-trigger').on('click', function () {
                        var jForm = $(this).closest('form');
                        jForm.submit();
                        return false;
                    });


                    $('#toggle-trace-messages').on('click', function () {
                        $('#trace-message-container').toggle();
                        return false;
                    });

                });
            });
        </script>


        <?php
    }

}