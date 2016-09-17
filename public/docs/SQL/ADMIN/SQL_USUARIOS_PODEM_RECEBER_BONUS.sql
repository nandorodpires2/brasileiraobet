/* USUARIO QUE PODEM RECEBER BONUS */
set		@bonus := 12;

select	u.usuario_nome,
			u.usuario_email,
			sum(l.lancamento_valor) as bonus
from		lancamento l
			inner join usuario u on l.usuario_id = u.usuario_id
where		l.lancamento_bonus = 1
group by u.usuario_id
having 	sum(l.lancamento_valor) < @bonus;