<?php
/**
 * This file contains the default.php
 *
 * @package PHPDraft\HTML
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */
/**
 * @var \PHPDraft\Model\APIBlueprintElement[]
 */

$base = $this->categories;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $this->base_data['TITLE']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <style>
        <?= file_get_contents(__DIR__ . '/index.css');?>
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1><?= $this->base_data['TITLE']; ?>
                <small><?= $this->base_data['HOST']; ?></small>
            </h1>
            <p class="lead"><?= $this->base_data['DESC']; ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2 method-nav">
            <?php foreach ($base as $category): ?>
                <?php if ($category->children !== []): ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <a href="#<?= $category->get_href(); ?>"><?= $category->title; ?></a>
                                <a class="btn-xs pull-right" role="button" data-toggle="collapse"
                                   href="#collapse-<?= $category->get_href(); ?>" aria-expanded="false"
                                   aria-controls="collapseMenu">
                                    <span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h3>
                        </div>
                        <div class="collapse collapsed in" id="collapse-<?= $category->get_href(); ?>">
                            <div class="panel-body">
                                <ul class="list-unstyled">
                                    <?php foreach ($category->children as $resource): ?>
                                        <li>
                                            <a href="#<?= $resource->get_href(); ?>"><?= $resource->title; ?></a>
                                        </li>
                                        <ul>
                                            <?php foreach ($resource->children as $transition): ?>
                                                <li>
                                                    <a href="#<?= $transition->get_href(); ?>">
                                                        <?= $transition->title; ?>
                                                        <span
                                                            class="pull-right <?= $this->get_method_icon($transition->get_method()); ?>"></span>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <div class="main-url"><?= $this->base_data['HOST']; ?></div>
        </div>
        <div class="col-md-10">
            <?php foreach ($base as $category): ?>
            <h2><a name="<?= $category->get_href(); ?>"><?= $category->title; ?></a></h2>
            <p><?= $category->description; ?></p>
            <?php foreach ($category->children as $resource): ?>
            <h3>
                <a name="<?= $resource->get_href(); ?>"><?= $resource->title; ?></a>
                <small><?= $resource->href; ?></small>
            </h3>
            <p><?php $resource->description; ?></p>
            <?php foreach ($resource->children as $transition): ?>
            <div
                class="panel panel-default <?= $transition->get_method(); ?>">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <var><?= $transition->get_method(); ?></var>
                        <code><?= $transition->href; ?></code>
                        <a class="pull-right transition-title"
                           name="<?= $transition->get_href(); ?>"><?= $transition->title; ?></a>
                    </h3>
                </div>
                <div class="panel-body">
                    <p class="lead"><?= $transition->description; ?></p>
                    <?php if ($transition->url_variables !== []): ?>
                        <h4>Example URI</h4>
                        <span class="base-url"><?= $this->base_data['HOST']; ?></span>
                        <em><?= $transition->build_url(); ?></em>
                    <?php endif; ?>

                    <h4 class="request">Request
                        <a class="btn-xs pull-right" role="button" data-toggle="collapse"
                           href="#request-coll-<?= $transition->get_href(); ?>" aria-expanded="false"
                           aria-controls="collapseMenu">
                            <span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                    <div class="collapse collapsed request-panel"
                         id="request-coll-<?= $transition->get_href(); ?>">
                        <?php if (isset($transition->request)): ?>
                            <?php if ($transition->request->headers !== []): ?>
                                <h5>Headers</h5>
                                <ul class="headers list-unstyled">
                                    <?php foreach ($transition->request->headers as $name => $value): ?>
                                        <li>
                                            <code><span class="attr"><?= $name; ?></span>: <span
                                                    class="value"><?= $value; ?></span>
                                            </code>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($transition->url_variables !== []): ?>
                            <h5>URI Parameters</h5>
                            <dl class="dl-horizontal">
                                <?php foreach ($transition->url_variables as $key => $value): ?>
                                    <?php $status = ($value->status === '') ? '' : '(' . $value->status . ')'; ?>
                                    <dt><?= $key; ?></dt>
                                    <dd>
                                        <code><?= $value->type; ?></code>
                                        <?= $status; ?>
                                        <samp><?= is_array($value->value) ? join('|', $value->value) : $value->value; ?></samp>
                                        <p><?= $value->description; ?></p>
                                    </dd>
                                <?php endforeach; ?>
                            </dl>
                        <?php endif; ?>

                        <?php if ($transition->data_variables !== []): ?>
                            <h5>Data object</h5>
                            <dl class="dl-horizontal">
                                <?php foreach ($transition->data_variables as $key => $value): ?>
                                    <?php $status = ($value->status === '') ? '' : '(' . $value->status . ')'; ?>
                                    <dt><?= $key; ?></dt>
                                    <dd>
                                        <code><?= $value->type; ?></code>
                                        <?= $status; ?>
                                        <samp><?= is_array($value->value) ? join('|', $value->value) : $value->value; ?></samp>
                                        <p><?= $value->description; ?></p>
                                    </dd>
                                <?php endforeach; ?>
                            </dl>
                        <?php endif; ?>
                    </div>
                    <?php if (isset($transition->responses)): ?>
                        <?php foreach ($transition->responses as $response): ?>
                            <h4 class="response <?= $this->get_response_status($response->statuscode); ?>">
                                Response <var><?= $response->statuscode; ?></var>
                                <a class="btn-xs pull-right" role="button" data-toggle="collapse"
                                   href="#request-coll--<?= $transition->get_href() . '-' . $response->statuscode; ?>"
                                   aria-expanded="false"
                                   aria-controls="collapseMenu">
                                    <span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                            <div class="collapse collapsed request-panel"
                                 id="request-coll--<?= $transition->get_href() . '-' . $response->statuscode; ?>">
                                <?php if ($response->headers !== []): ?>
                                    <h5>Headers</h5>
                                    <ul class="headers list-unstyled">
                                        <?php foreach ($response->headers as $name => $value): ?>
                                            <li>
                                                <code><span class="attr"><?= $name; ?></span>: <span
                                                        class="value"><?= $value; ?></span>
                                                </code>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <?php if ($response->structure !== []): ?>
                                    <h5>Data Structure</h5>
                                    <dl class="dl-horizontal">
                                        <?php foreach ($response->structure[0]['struct']->value as $value): ?>
                                            <dt><?= $value->key; ?></dt>
                                            <dd>
                                                <code><?= $value->type; ?></code>
                                                <span><?= $value->description; ?></span>
                                                <?php if (isset($value->value->element) && $value->value->element === 'object')
                                                    : ?>
                                                    <?= $this->get_data_structure($value->value); ?>
                                                <?php else: ?>
                                                    <blockquote><?= $value->value; ?></blockquote>
                                                <?php endif; ?>

                                            </dd>
                                        <?php endforeach; ?>
                                    </dl>
                                <?php endif; ?>
                                <?php foreach ($response->content as $key => $value): ?>
                                    <h5><?= $key; ?></h5>
                                    <pre><?= $value; ?></pre>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php foreach ($transition->structures as $structure): ?>
                        <?php var_dump($structure);?>
<!--                    --><?php //if (isset($this->base_structures[$structure])): ?>
<!--                    <h5>--><?//= $structure; ?><!-- Object</h5>-->
<!--                    <ul class="structure list-unstyled">-->
<!--                        <li>-->
<!--                            <pre>--><?php //var_dump($this->base_structures[$structure]['deps']); ?><!--</pre>-->
<!--                        </li>-->
<!--                        --><?php //foreach ($this->base_structures[$structure]['deps'] as $dep):?>
<!--                            --><?php //if (isset($this->base_structures[$dep])): ?>
<!--                                <li>-->
<!--                                    <pre>--><?php //var_dump($this->base_structures[$dep]['struct']);?><!--</pre>-->
<!--                                </li>-->
<!--                            --><?php //endif;?>
<!--                        --><?php //endforeach;?>
<!--                    </ul>-->
<!--                    --><?php //endif;?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
        integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
        crossorigin="anonymous"></script>
</body>
</html>