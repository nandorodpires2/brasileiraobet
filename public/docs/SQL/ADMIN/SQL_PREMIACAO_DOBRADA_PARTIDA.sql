/* OS VENCEDORES RECEBERÃO PREMIO EM DOBRO */
select	u.usuario_nome,
			u.usuario_email,
			a.aposta_vencedora_premio,
			a.aposta_vencedora_valor
from		aposta a
			inner join partida p on a.partida_id = p.partida_id
			inner join usuario u on a.usuario_id = u.usuario_id
where		p.partida_id = 52
			and a.aposta_vencedora = 1
order by a.aposta_vencedora_valor asc
			