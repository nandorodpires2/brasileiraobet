/* APOSTAS PARTIDAS */
select	a.aposta_id,
			u.usuario_nome,
			u.usuario_email,
			t1.time_nome,
			t2.time_nome,			
			concat(a.aposta_placar_mandante, ' - ', a.aposta_placar_visitante) as placar,
			a.aposta_resultado,
			p.partida_vencedores_premio1,
			p.partida_valor_premio1,
			p.partida_vencedores_premio2,
			p.partida_valor_premio2,
			p.partida_vencedores_premio3,
			p.partida_valor_premio3,
			a.aposta_vencedora
from		aposta a
			inner join partida p on a.partida_id = p.partida_id
			inner join usuario u on a.usuario_id = u.usuario_id
			inner join time t1 on p.time_id_mandante = t1.time_id
			inner join time t2 on p.time_id_visitante = t2.time_id
where		p.partida_serie = 1
			and a.aposta_processada = 0
order by p.partida_data desc,
			concat(a.aposta_placar_mandante, ' - ', a.aposta_placar_visitante) asc,
			u.usuario_id
			