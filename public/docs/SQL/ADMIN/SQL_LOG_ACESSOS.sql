/* LGO ACESSOS */
select	u.usuario_nome,
			u.usuario_email,
			count(l.log_acesso_id) as acessos
from		log_acesso l
			inner join usuario u on l.usuario_id = u.usuario_id
group by u.usuario_id
order by count(l.log_acesso_id) desc