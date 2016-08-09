set 		@mandante := 'santos';
set 		@visitante := 'cruzeiro';

select	u.usuario_nome,
			u.usuario_email,
			a.aposta_vencedora_premio,
			a.aposta_vencedora_valor
from		aposta a
			inner join usuario u on a.usuario_id = u.usuario_id
			inner join partida p on a.partida_id = p.partida_id
			inner join time t1 on p.time_id_mandante = t1.time_id
			inner join time t2 on p.time_id_visitante = t2.time_id
where		(
				t1.time_nome = @mandante
				and t2.time_nome = @visitante
			)
			and a.aposta_vencedora = 1
