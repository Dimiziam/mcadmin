DROP TRIGGER IF EXISTS transactions_insert;
DROP TRIGGER IF EXISTS transactions_update;
DROP TRIGGER IF EXISTS transactions_delete;

delimiter |

CREATE TRIGGER `transactions_insert` AFTER INSERT ON `transactions`
  FOR EACH ROW
BEGIN

	if NEW.status = 'done' then
		/* Change user amount */
		if NEW.type = 'add' then
			update users set
				amount = amount + NEW.amount
			where id = NEW.user_id;
		else
			update users set
				amount = amount - NEW.amount
			where id = NEW.user_id;
		end if;
	end if;
	
END;

|

CREATE TRIGGER `transactions_update` AFTER UPDATE ON `transactions`
  FOR EACH ROW
BEGIN

	if OLD.status = 'done' then
		/* Change user amount for old value */
		if OLD.type = 'add' then
			update users set
				amount = amount - OLD.amount
			where id = OLD.user_id;
		else
			update users set
				amount = amount + OLD.amount
			where id = OLD.user_id;
		end if;
	end if;

	if NEW.status = 'done' then
		/* Change user amount for new value */
		if NEW.type = 'add' then
			update users set
				amount = amount + NEW.amount
			where id = NEW.user_id;
		else
			update users set
				amount = amount - NEW.amount
			where id = NEW.user_id;
		end if;
	end if;
	
END;

|

CREATE TRIGGER `transactions_delete` AFTER DELETE ON `transactions`
  FOR EACH ROW
BEGIN

	if OLD.status = 'done' then
		/* Change user amount */
		if OLD.type = 'add' then
			update users set
				amount = amount - OLD.amount
			where id = OLD.user_id;
		else
			update users set
				amount = amount + OLD.amount
			where id = OLD.user_id;
		end if;
	end if;

END;

|
delimiter ;
