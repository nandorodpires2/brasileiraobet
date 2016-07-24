/* VENCEDORES RODADA */
select	t1.time_nome,
			t2.time_nome,
			u.usuario_nome,
			u.usuario_email,
			a.aposta_vencedora_premio,
			a.aposta_vencedora_valor
from		aposta a
			inner join usuario u on a.usuario_id = u.usuario_id
			inner join partida p on a.partida_id = p.partida_id
			inner join time t1 on p.time_id_mandante = t1.time_id
			inner join time t2 on p.time_id_visitante = t2.time_id
where		p.partida_rodada = 16
			and p.partida_serie = 1
			and a.aposta_vencedora = 1
order by a.aposta_vencedora_premio asc
