/* MAIORES VENCEDORES */
select	u.usuario_nome,
			u.usuario_email,
			count(*) as apostas,
			sum(p.partida_montante / p.partida_vencedores) as total
from		usuario u
			inner join aposta a on u.usuario_id = a.usuario_id
			inner join partida p on a.partida_id = p.partida_id
where		a.aposta_vencedora = 1
group by u.usuario_id
order by sum(p.partida_montante / p.partida_vencedores) desc