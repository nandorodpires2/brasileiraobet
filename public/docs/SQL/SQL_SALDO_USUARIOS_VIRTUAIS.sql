/* SALDO USUARIOS */
select	u.usuario_id,
			u.usuario_nome,
			u.usuario_email,
			u.usuario_senha,
			sum(l.lancamento_valor) as saldo
from		usuario u
			inner join lancamento l on u.usuario_id = l.usuario_id
where		u.usuario_nome like '%VIRTUAL%'
group by u.usuario_id
having 	sum(l.lancamento_valor) >= 3
order by sum(l.lancamento_valor) desc
