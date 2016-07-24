/* USUARIOS QUE PODEM PEDIR RESGATE */
select	u.usuario_id,
			u.usuario_nome,
			u.usuario_email,
			sum(l.lancamento_valor) as saldo
from		usuario u
			inner join lancamento l on u.usuario_id = l.usuario_id
where		l.lancamento_bonus = 0
group by u.usuario_id
having	sum(l.lancamento_valor) > 150
order by sum(l.lancamento_valor) desc

