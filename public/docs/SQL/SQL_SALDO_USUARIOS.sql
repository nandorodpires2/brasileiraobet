/* SALDO USUARIOS */
select	u.usuario_id,
			u.usuario_nome,
			u.usuario_email,
			sum(l.lancamento_valor) as saldo
from		usuario u
			inner join lancamento l on u.usuario_id = l.usuario_id
group by u.usuario_id
order by sum(l.lancamento_valor) desc

