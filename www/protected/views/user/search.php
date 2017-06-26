<?php
// Get user translations
$translations = MTLang::instance()->getGroup('user');

// Prepare module menu
$this->menu = array(
    array('label' => MTLang::t('add_new', 'persistence'), 'url' => $this->createUrl('/user/add/')),
);
?>

    <script>
        var global_messages = {
            'user': {
                'delete_confirm': "<?php echo $translations['delete_confirm']; ?>",
                'delete_error': "<?php echo $translations['message_3']; ?>",
                'disabled_message': "<?php echo $translations['message_5']; ?>",
                'status_no': "<b><?php echo MTLang::t('no_value', 'persistence'); ?></b>"
            }
        };
    </script>

<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'search-form',
)); ?>

    <div class="form">

        <div class="row">
            Email:
            <?php
            $this->widget('CAutoComplete',
                array(
                    'model' => 'SearchUserForm',
                    'name' => 'email',
                    'value' => !empty($model->email) ? $model->email : '',
                    'url' => $this->createUrl('/user/autocomplete/'),
                    'minChars' => 2,
                    'max' => 10,
                    'htmlOptions' => array(
                        'size' => 50
                    ),
                    'methodChain' => '.result(function(event,item){$("#SearchUserForm_id").attr("value", item[1]);})'
                )
            );
            echo CHtml::hiddenField('SearchUserForm[id]', !empty($model->id) ? $model->id : 0, array('id' => 'SearchUserForm_id'));
            ?>
        </div>
        <div class="row_label">
            <?php echo $form->labelEx($model, 'show_download_all'); ?>&nbsp;
            <?php echo $form->checkBox($model, 'show_download_all'); ?>
            <?php echo $form->error($model, 'show_download_all'); ?>
        </div>


        <div class="row buttons">
            <input type="submit" value="<?php echo MTLang::t('find_button', 'persistence'); ?>"
                   onClick='$("#pager_currentPageField").attr("value", 1);'>
        </div>

    </div>

<?php
if (!empty($users)) {
    // Get users items
    $items = $users->getData();

    $statuses = array(
        'yes' => MTLang::t('yes_value', 'persistence'),
        'no' => '<b>' . MTLang::t('no_value', 'persistence') . '</b>'
    );

    // Add pager widget
    $this->widget('application.components.widgets.WPager', array('provider' => $users));
    ?>
    <table class="table table-striped">
        <tr>
            <th>ID</th>
            <th><?php echo $translations['email']; ?></th>
            <th><?php echo $translations['name']; ?><br>
                <?php echo $translations['last_name']; ?>
            </th>
            <th><?php echo $translations['amount']; ?></th>
            <!-- <th><?php echo $translations['is_active']; ?></th> -->
            <th>Flags</th>

            <th><?php echo $translations['email_affiliate']; ?></th>
            <th>
                <?php echo $translations['created']; ?><br>
                <?php echo $translations['last_login_time']; ?>
            </th>
            <th>
                <?php echo $translations['ip_register']; ?><br>
                <?php echo $translations['last_ip_login']; ?>
            </th>
			<th>
			  <?php echo $translations['subscribtion']; ?><br>
			</th>
            <th><?php echo $translations['country_code']; ?></th>
            <th></th>

            <th colspan="3">&nbsp;</th>
        </tr>
        <?php foreach ($items as $item) { ?>
            <tr id="user_<?php echo $item['id']; ?>">
                <td><?php echo $item['id']; ?></td>
                <td><?php echo $item['email']; ?></td>
                <td><?php echo $item['name']; ?><br><?php echo $item['last_name']; ?></td>
                <td><?php echo $item['amount'] > 0 ? '<b>' . $item["amount"] . ' </b>' : $item["amount"]; ?></td>
                <!-- <td id="user_status_<?php echo $item['id']; ?>"><?php echo $statuses[$item['is_active']]; ?></td> -->
                <td nowrap><?php echo $translations['is_affiliate']; ?>
                    :&nbsp;<?php echo $statuses[$item['is_affiliate']]; ?><br>
					<span <?php
                    if ($item['download_all'] === 'yes') {
                        echo "class='green bold'";
                    }
                    ?>><?php echo $translations['download_all']; ?>
                        :&nbsp;<?php echo $statuses[$item['download_all']]; ?></span>
                    <br>
                    <?php echo $translations['is_email_confirmed']; ?>
                    :&nbsp;<?php echo $statuses[$item['is_email_confirmed']]; ?></td>
                <td><?php echo $item['email_affiliate']; ?></td>
                <td nowrap>
                    <?php echo $translations['created']; ?>:<br><?php echo $item['created']; ?><br>
                    <?php echo $translations['last_login_time']; ?>
                    :<br><?php echo $item['last_login_time'] ? date("d.m.Y H:i", $item['last_login_time']) : ""; ?>
                </td>
                <td>
                    <?php echo $item['ip_register']; ?><br>
                    <?php echo $item['last_ip_login']; ?><br>
                    <span class="referer_comment"><?php echo $item['http_referer']; ?></span>

                </td>
				<td>
				  <?php if ($item['last_time_sent_subsr']!=0) {
				  	echo $translations['last_time_sent_subsr']." ".date("d.m.Y H:i",$item['last_time_sent_subsr']);
				  	echo "<br>";
				  }
				  echo $translations['is_subscribed'];
				  echo $item['is_subscribed'] ? ": Yes":": No";
				  ?><br>
				</td>
                <td><?php echo $item['country_code']; ?></td>
                <td>
                    <?php if (!empty($item['facebook_id'])) {?>
                        <a target="_blank" href="https://www.facebook.com/app_scoped_user_id/<?php echo $item['facebook_id'];?>/">
                            <img id="fb_squarepic" width="32" height="32" alt="Facebook" src="https://graph.facebook.com/<?php echo $item['facebook_id'];?>/picture" title="Facebook">
                        </a>
                    <?php }?>
                </td>
                <td>
                    <a href="<?php echo $this->createUrl('/user/edit/', array('id' => $item['id'])); ?>"
                       title="<?php echo $translations['edit_link_title']; ?>">
                        <img src="/images/edit_16.png" border="0">
                    </a>
                </td>

                <td>
                    <a href="JavaScript: void(0);" onClick="user_delete(<?php echo $item['id']; ?>);"
                       title="<?php echo $translations['delete_link_title']; ?>">
                        <img src="/images/trash_16.png" border="0">
                    </a>
                </td>
                <td>
                    <a href="<?php echo $this->createUrl('/transaction/search/',
						array('SearchTransactionForm[user_id]' => $item['id'],
							'SearchTransactionForm[status]' => '',
							'SearchTransactionForm[billing]' => ''
							)
						); ?>"
                       title="<?php echo $translations['payments_link_title']; ?>">
                        <img src="/images/coins_16.png" border="0">
                    </a>
                </td>
                <td>
                    <a href="<?php echo $this->createUrl('/transaction/add/', array('userId' => $item['id'])); ?>"
                       title="<?php echo $translations['change_balance_link_title']; ?>">
                        <img src="/images/coins_add_16.png" border="0">
                    </a>
                </td>
                <td>
                    <a href="<?php echo $this->createUrl('/order/search/', array('SearchOrderForm[user_id]' => $item['id'])); ?>"
                       title="<?php echo $translations['orders_link_title']; ?>">
                        <img src="/images/cart_16.png" border="0">
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } else { ?>
    <h2><?php echo $translations['users_not_found']; ?></h2>
<?php } ?>
    <script language="javascript">
        $(document).ready(function () {
            $(".referer_comment").shorten({
				showChars: 30,
			});

        });
    </script>
<?php $this->endWidget(); ?>