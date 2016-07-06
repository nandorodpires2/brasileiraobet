/* SALDO USUARIOS */
select	u.usuario_id,
			u.usuario_nome,
			u.usuario_email,
			sum(l.lancamento_valor) as saldo
from		usuario u
			inner join lancamento l on u.usuario_id = l.usuario_id
where		u.usuario_nome like 'USUÁRIO VIRTUAL%'
group by u.usuario_id
having 	sum(l.lancamento_valor) >= 3
order by sum(l.lancamento_valor) asc
